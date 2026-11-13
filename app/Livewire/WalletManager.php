<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class WalletManager extends Component
{
    public bool $isFormOpen = false;

    public ?int $editingWalletId = null;

    // Form fields
    public string $name = '';

    public string $type = 'bank';

    public float $currentBalance = 0;

    public bool $isExcludedFromSts = false;

    public string $accountNumber = '';

    public string $color = '#4B3FCB';

    // Transfer fields
    public bool $isTransferOpen = false;

    public ?int $fromWalletId = null;

    public ?int $toWalletId = null;

    public float $transferAmount = 0;

    public string $transferNote = '';

    public function openCreate(): void
    {
        $user = auth()->user();
        if ($user->plan_tier !== 'pro' && $user->wallets()->where('is_active', true)->count() >= 3) {
            session()->flash('error', 'Batas kuota Starter adalah maksimal 3 dompet. Upgrade ke Pro untuk dompet tanpa batas.');

            return;
        }

        $this->reset(['name', 'currentBalance', 'isExcludedFromSts', 'accountNumber']);
        $this->type = 'bank';
        $this->color = '#4B3FCB';
        $this->editingWalletId = null;
        $this->isFormOpen = true;
    }

    public function openEdit(int $id): void
    {
        $w = auth()->user()->wallets()->findOrFail($id);
        $this->editingWalletId = $w->id;
        $this->name = $w->name;
        $this->type = $w->type;
        $this->currentBalance = (float) $w->current_balance;
        $this->isExcludedFromSts = (bool) $w->is_excluded_from_sts;
        $this->accountNumber = (string) $w->account_number;
        $this->color = $w->color ?: '#4B3FCB';
        $this->isFormOpen = true;
    }

    public function saveWallet(): void
    {
        $this->validate([
            'name' => 'required|string|max:50',
            'type' => 'required|in:cash,bank,ewallet,credit,savings',
            'currentBalance' => 'required|numeric',
            'color' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'current_balance' => $this->currentBalance,
            'is_excluded_from_sts' => $this->isExcludedFromSts,
            'account_number' => $this->accountNumber ?: null,
            'color' => $this->color,
        ];

        if ($this->editingWalletId) {
            $w = auth()->user()->wallets()->findOrFail($this->editingWalletId);
            $w->update($data);
            session()->flash('success', "Dompet {$this->name} berhasil diperbarui.");
        } else {
            $data['user_id'] = auth()->id();
            Wallet::create($data);
            session()->flash('success', "Dompet {$this->name} berhasil ditambahkan.");
        }

        $this->isFormOpen = false;
        $this->dispatch('wallet-updated');
    }

    public function openTransfer(): void
    {
        $wallets = auth()->user()->wallets()->where('is_active', true)->get();
        if ($wallets->count() < 2) {
            session()->flash('error', 'Minimal perlu 2 dompet aktif untuk melakukan transfer.');

            return;
        }

        $this->fromWalletId = $wallets[0]->id;
        $this->toWalletId = $wallets[1]->id;
        $this->transferAmount = 0;
        $this->transferNote = '';
        $this->isTransferOpen = true;
    }

    public function executeTransfer(): void
    {
        $this->validate([
            'fromWalletId' => 'required|exists:wallets,id',
            'toWalletId' => 'required|exists:wallets,id|different:fromWalletId',
            'transferAmount' => 'required|numeric|min:1',
        ]);

        $from = auth()->user()->wallets()->findOrFail($this->fromWalletId);
        $to = auth()->user()->wallets()->findOrFail($this->toWalletId);

        DB::transaction(function () use ($from, $to) {
            Transaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $from->id,
                'to_wallet_id' => $to->id,
                'type' => 'transfer',
                'amount' => $this->transferAmount,
                'transaction_date' => Carbon::today(),
                'note' => $this->transferNote ?: "Transfer {$from->name} → {$to->name}",
            ]);

            $from->decrement('current_balance', $this->transferAmount);
            $to->increment('current_balance', $this->transferAmount);
        });

        $this->isTransferOpen = false;
        session()->flash('success', 'Transfer saldo berhasil dilakukan.');
        $this->dispatch('wallet-updated');
    }

    public function render()
    {
        $wallets = auth()->user()->wallets()->where('is_active', true)->get();
        $totalNet = $wallets->sum('current_balance');
        $totalSts = $wallets->where('is_excluded_from_sts', false)->sum('current_balance');

        return view('livewire.wallet-manager', compact('wallets', 'totalNet', 'totalSts'));
    }
}
