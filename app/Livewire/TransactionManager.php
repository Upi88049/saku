<?php

namespace App\Livewire;

use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class TransactionManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filterType = '';

    public ?int $filterWallet = null;

    public ?int $filterCategory = null;

    public string $startDate = '';

    public string $endDate = '';

    protected $listeners = [
        'transaction-saved' => '$refresh',
    ];

    public function mount(): void
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteTransaction(int $id): void
    {
        $tx = auth()->user()->transactions()->findOrFail($id);

        DB::transaction(function () use ($tx) {
            // Rollback wallet balance
            $wallet = Wallet::find($tx->wallet_id);
            if ($wallet) {
                if ($tx->type === 'income') {
                    $wallet->decrement('current_balance', $tx->amount);
                } elseif ($tx->type === 'expense') {
                    $wallet->increment('current_balance', $tx->amount);
                } elseif ($tx->type === 'transfer' && $tx->to_wallet_id) {
                    $wallet->increment('current_balance', $tx->amount);
                    $toWallet = Wallet::find($tx->to_wallet_id);
                    $toWallet?->decrement('current_balance', $tx->amount);
                }
            }

            $tx->delete();
        });

        session()->flash('success', 'Transaksi berhasil dihapus.');
    }

    public function exportCsv()
    {
        $transactions = $this->getQuery()->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="saku-transaksi-'.date('Y-m-d').'.csv"',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($handle, ['Tanggal', 'Tipe', 'Kategori', 'Dompet', 'Nominal', 'Catatan']);

            foreach ($transactions as $t) {
                fputcsv($handle, [
                    $t->transaction_date->format('Y-m-d'),
                    ucfirst($t->type),
                    $t->category?->name ?? ($t->type === 'transfer' ? 'Transfer' : '-'),
                    $t->wallet?->name.($t->toWallet ? ' -> '.$t->toWallet->name : ''),
                    $t->amount,
                    $t->note,
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function getQuery()
    {
        $query = auth()->user()->transactions()
            ->with(['wallet', 'category', 'toWallet'])
            ->latest('transaction_date')
            ->latest('id');

        if (! empty($this->search)) {
            $query->where('note', 'like', '%'.$this->search.'%');
        }

        if (! empty($this->filterType)) {
            $query->where('type', $this->filterType);
        }

        if (! empty($this->filterWallet)) {
            $query->where('wallet_id', $this->filterWallet);
        }

        if (! empty($this->filterCategory)) {
            $query->where('category_id', $this->filterCategory);
        }

        if (! empty($this->startDate)) {
            $query->whereDate('transaction_date', '>=', $this->startDate);
        }

        if (! empty($this->endDate)) {
            $query->whereDate('transaction_date', '<=', $this->endDate);
        }

        return $query;
    }

    public function render()
    {
        $transactions = $this->getQuery()->paginate(15);
        $wallets = auth()->user()->wallets()->where('is_active', true)->get();
        $categories = auth()->user()->categories()->get();

        return view('livewire.transaction-manager', compact('transactions', 'wallets', 'categories'));
    }
}
