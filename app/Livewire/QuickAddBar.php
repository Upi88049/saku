<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class QuickAddBar extends Component
{
    public string $query = '';

    public ?array $preview = null;

    public string $statusMessage = '';

    public function updatedQuery(): void
    {
        $this->parseQuery();
    }

    public function parseQuery(): void
    {
        $q = trim($this->query);
        if (empty($q)) {
            $this->preview = null;

            return;
        }

        // Match amount with suffixes: rb, ribu, k, jt, juta
        preg_match('/(\d+(?:[.,]\d+)*)\s*(rb|ribu|k|jt|juta)?/i', $q, $m);
        $amount = 0;
        if (! empty($m)) {
            $raw = $m[1];
            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $raw)) {
                $raw = str_replace('.', '', $raw);
            }
            $v = (float) str_replace(',', '.', $raw);
            $unit = strtolower($m[2] ?? '');
            if (in_array($unit, ['rb', 'ribu', 'k'])) {
                $v *= 1000;
            } elseif (in_array($unit, ['jt', 'juta'])) {
                $v *= 1000000;
            }
            $amount = round($v);
        }

        if ($amount <= 0) {
            $this->preview = null;

            return;
        }

        $type = preg_match('/gaji|terima|masuk|bonus|inflow/i', $q) ? 'income' : 'expense';

        // Detect wallet
        $wallets = auth()->user()->wallets;
        $targetWallet = null;
        if (preg_match('/gopay/i', $q)) {
            $targetWallet = $wallets->firstWhere('name', 'GoPay') ?? $wallets->first();
        } elseif (preg_match('/tunai|cash/i', $q)) {
            $targetWallet = $wallets->firstWhere('name', 'Kas Tunai') ?? $wallets->first();
        } else {
            $targetWallet = $wallets->firstWhere('name', 'Bank Utama') ?? $wallets->first();
        }

        // Detect category
        $categories = auth()->user()->categories;
        $targetCategory = null;
        if ($type === 'income') {
            $targetCategory = $categories->where('type', 'income')->firstWhere('name', 'Gaji')
                ?? $categories->where('type', 'income')->first();
        } else {
            if (preg_match('/kopi|makan|bakso|nasi|kafe|warteg|jajan|ayam/i', $q)) {
                $targetCategory = $categories->firstWhere('name', 'Makan & Minum');
            } elseif (preg_match('/grab|gojek|ojek|bensin|tol|parkir|mrt/i', $q)) {
                $targetCategory = $categories->firstWhere('name', 'Transport');
            } elseif (preg_match('/netflix|spotify|langganan|youtube/i', $q)) {
                $targetCategory = $categories->firstWhere('name', 'Langganan');
            } elseif (preg_match('/admin|transfer|biaya/i', $q)) {
                $targetCategory = $categories->firstWhere('name', 'Biaya Admin & Transfer');
            }
            $targetCategory = $targetCategory ?? $categories->where('type', 'expense')->first();
        }

        // Clean description
        $desc = $q;
        if (! empty($m[0])) {
            $desc = str_replace($m[0], '', $desc);
        }
        $desc = preg_replace('/gopay|bca|tunai|cash|bank|masuk|keluar/i', '', $desc);
        $desc = trim(preg_replace('/\s+/', ' ', $desc));
        $desc = ucwords($desc ?: 'Transaksi Cepat');

        $this->preview = [
            'type' => $type,
            'type_label' => $type === 'income' ? 'Pemasukan' : 'Pengeluaran',
            'amount' => $amount,
            'amount_formatted' => 'Rp '.number_format($amount, 0, ',', '.'),
            'description' => $desc,
            'wallet_id' => $targetWallet?->id,
            'wallet_name' => $targetWallet?->name ?? 'Default',
            'category_id' => $targetCategory?->id,
            'category_name' => $targetCategory?->name ?? 'Umum',
        ];
    }

    public function save(): void
    {
        if (! $this->preview) {
            return;
        }

        DB::transaction(function () {
            $tx = Transaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $this->preview['wallet_id'],
                'category_id' => $this->preview['category_id'],
                'type' => $this->preview['type'],
                'amount' => $this->preview['amount'],
                'transaction_date' => Carbon::today(),
                'note' => $this->preview['description'],
                'is_recurring' => false,
            ]);

            // Update wallet balance
            $wallet = Wallet::find($this->preview['wallet_id']);
            if ($wallet) {
                if ($this->preview['type'] === 'income') {
                    $wallet->increment('current_balance', $this->preview['amount']);
                } else {
                    $wallet->decrement('current_balance', $this->preview['amount']);
                }
            }
        });

        $this->statusMessage = 'Tersimpan: '.$this->preview['description'];
        $this->query = '';
        $this->preview = null;

        $this->dispatch('transaction-saved');
    }

    public function render()
    {
        return view('livewire.quick-add-bar');
    }
}
