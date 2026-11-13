<?php

namespace App\Livewire;

use App\Models\DebtPayment;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DebtManager extends Component
{
    public string $activeTab = 'receivable'; // receivable, debt

    // Modal Add
    public bool $isFormOpen = false;

    public string $formType = 'receivable';

    public string $contactName = '';

    public string $contactPhone = '';

    public float $amount = 0;

    public string $dueDate = '';

    public string $note = '';

    // Modal Payment
    public bool $isPaymentOpen = false;

    public ?int $selectedDebtId = null;

    public float $paymentAmount = 0;

    public ?int $paymentWalletId = null;

    public string $paymentDate = '';

    public string $paymentNote = '';

    public function mount(): void
    {
        $this->dueDate = Carbon::today()->addDays(7)->format('Y-m-d');
        $this->paymentDate = Carbon::today()->format('Y-m-d');
    }

    public function openCreate(string $type = 'receivable'): void
    {
        $this->formType = $type;
        $this->reset(['contactName', 'contactPhone', 'amount', 'note']);
        $this->dueDate = Carbon::today()->addDays(7)->format('Y-m-d');
        $this->isFormOpen = true;
    }

    public function saveDebt(): void
    {
        $this->validate([
            'contactName' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
            'dueDate' => 'nullable|date',
        ]);

        auth()->user()->debtReceivables()->create([
            'type' => $this->formType,
            'contact_name' => $this->contactName,
            'contact_phone' => $this->contactPhone ?: null,
            'total_amount' => $this->amount,
            'remaining_amount' => $this->amount,
            'due_date' => $this->dueDate ?: null,
            'status' => 'active',
            'note' => $this->note ?: ($this->formType === 'receivable' ? 'Piutang baru' : 'Utang baru'),
        ]);

        $this->isFormOpen = false;
        session()->flash('success', 'Catatan berhasil ditambahkan.');
        $this->dispatch('debt-updated');
    }

    public function openPayment(int $id): void
    {
        $debt = auth()->user()->debtReceivables()->findOrFail($id);
        $this->selectedDebtId = $debt->id;
        $this->paymentAmount = (float) $debt->remaining_amount;
        $defaultWallet = auth()->user()->wallets()->where('is_active', true)->first();
        $this->paymentWalletId = $defaultWallet?->id;
        $this->paymentDate = Carbon::today()->format('Y-m-d');
        $this->paymentNote = 'Pelunasan / cicilan';
        $this->isPaymentOpen = true;
    }

    public function savePayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:1',
            'paymentWalletId' => 'required|exists:wallets,id',
            'paymentDate' => 'required|date',
        ]);

        $debt = auth()->user()->debtReceivables()->findOrFail($this->selectedDebtId);
        $wallet = auth()->user()->wallets()->findOrFail($this->paymentWalletId);

        DB::transaction(function () use ($debt, $wallet) {
            $paid = min($this->paymentAmount, $debt->remaining_amount);

            // Create Transaction record
            $txType = $debt->type === 'receivable' ? 'income' : 'expense';
            $tx = Transaction::create([
                'user_id' => auth()->id(),
                'wallet_id' => $wallet->id,
                'type' => $txType,
                'amount' => $paid,
                'transaction_date' => $this->paymentDate,
                'note' => "Pembayaran {$debt->contact_name} ({$debt->note})",
            ]);

            // Create Debt Payment log
            DebtPayment::create([
                'debt_receivable_id' => $debt->id,
                'transaction_id' => $tx->id,
                'paid_amount' => $paid,
                'payment_date' => $this->paymentDate,
                'note' => $this->paymentNote,
            ]);

            // Adjust wallet
            if ($txType === 'income') {
                $wallet->increment('current_balance', $paid);
            } else {
                $wallet->decrement('current_balance', $paid);
            }

            // Adjust remaining debt amount
            $newRemaining = max(0, $debt->remaining_amount - $paid);
            $debt->update([
                'remaining_amount' => $newRemaining,
                'status' => $newRemaining <= 0 ? 'settled' : 'active',
            ]);
        });

        $this->isPaymentOpen = false;
        session()->flash('success', 'Pembayaran berhasil dicatat.');
        $this->dispatch('debt-updated');
    }

    public function render()
    {
        $user = auth()->user();
        $items = $user->debtReceivables()
            ->where('type', $this->activeTab)
            ->with('payments')
            ->orderBy('status')
            ->orderBy('due_date')
            ->get();

        $totalReceivable = $user->debtReceivables()->where('type', 'receivable')->where('status', 'active')->sum('remaining_amount');
        $totalDebt = $user->debtReceivables()->where('type', 'debt')->where('status', 'active')->sum('remaining_amount');
        $wallets = $user->wallets()->where('is_active', true)->get();

        return view('livewire.debt-manager', compact('items', 'totalReceivable', 'totalDebt', 'wallets'));
    }
}
