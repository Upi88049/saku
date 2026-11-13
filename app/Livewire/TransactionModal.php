<?php

namespace App\Livewire;

use App\Models\DebtReceivable;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class TransactionModal extends Component
{
    use WithFileUploads;

    public bool $isOpen = false;

    public string $type = 'expense'; // expense, income, transfer

    public float $amount = 0;

    public ?int $walletId = null;

    public ?int $toWalletId = null;

    public ?int $categoryId = null;

    public string $transactionDate = '';

    public string $note = '';

    public $receipt = null;

    // Split Bill fields
    public bool $isSplitBill = false;

    public string $splitFriends = ''; // e.g., "Andi: 38000, Budi: 50000" or just names

    public float $personalAmount = 0;

    protected $listeners = [
        'open-transaction-modal' => 'open',
    ];

    public function mount(): void
    {
        $this->transactionDate = Carbon::today()->format('Y-m-d');
        $this->initDefaults();
    }

    public function initDefaults(): void
    {
        $user = auth()->user();
        if ($user) {
            $defaultWallet = $user->wallets()->where('is_active', true)->first();
            $this->walletId = $defaultWallet?->id;

            $defaultCategory = $user->categories()->where('type', $this->type)->first();
            $this->categoryId = $defaultCategory?->id;
        }
    }

    public function open(): void
    {
        $this->resetErrorBag();
        $this->amount = 0;
        $this->note = '';
        $this->receipt = null;
        $this->isSplitBill = false;
        $this->splitFriends = '';
        $this->personalAmount = 0;
        $this->transactionDate = Carbon::today()->format('Y-m-d');
        $this->initDefaults();
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
        $user = auth()->user();
        if ($user && $type !== 'transfer') {
            $defaultCategory = $user->categories()->where('type', $type)->first();
            $this->categoryId = $defaultCategory?->id;
        }
    }

    public function save(): void
    {
        $rules = [
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:1',
            'walletId' => 'required|exists:wallets,id',
            'transactionDate' => 'required|date',
            'note' => 'nullable|string|max:255',
            'receipt' => 'nullable|image|max:2048',
        ];

        if ($this->type === 'transfer') {
            $rules['toWalletId'] = 'required|exists:wallets,id|different:walletId';
        } else {
            $rules['categoryId'] = 'required|exists:categories,id';
        }

        $this->validate($rules);

        $receiptPath = null;
        if ($this->receipt) {
            $receiptPath = $this->receipt->store('receipts', 'public');
        }

        DB::transaction(function () use ($receiptPath) {
            $userId = auth()->id();
            $wallet = Wallet::findOrFail($this->walletId);

            if ($this->type === 'transfer') {
                $toWallet = Wallet::findOrFail($this->toWalletId);

                Transaction::create([
                    'user_id' => $userId,
                    'wallet_id' => $wallet->id,
                    'to_wallet_id' => $toWallet->id,
                    'type' => 'transfer',
                    'amount' => $this->amount,
                    'transaction_date' => $this->transactionDate,
                    'note' => $this->note ?: "Transfer ke {$toWallet->name}",
                    'receipt_path' => $receiptPath,
                ]);

                $wallet->decrement('current_balance', $this->amount);
                $toWallet->increment('current_balance', $this->amount);

            } elseif ($this->type === 'income') {
                Transaction::create([
                    'user_id' => $userId,
                    'wallet_id' => $wallet->id,
                    'category_id' => $this->categoryId,
                    'type' => 'income',
                    'amount' => $this->amount,
                    'transaction_date' => $this->transactionDate,
                    'note' => $this->note ?: 'Pemasukan',
                    'receipt_path' => $receiptPath,
                ]);

                $wallet->increment('current_balance', $this->amount);

            } else { // expense
                $personal = $this->isSplitBill && $this->personalAmount > 0 ? $this->personalAmount : $this->amount;

                $tx = Transaction::create([
                    'user_id' => $userId,
                    'wallet_id' => $wallet->id,
                    'category_id' => $this->categoryId,
                    'type' => 'expense',
                    'amount' => $this->amount,
                    'personal_amount' => $this->isSplitBill ? $personal : null,
                    'transaction_date' => $this->transactionDate,
                    'note' => $this->note ?: 'Pengeluaran',
                    'receipt_path' => $receiptPath,
                ]);

                $wallet->decrement('current_balance', $this->amount);

                // Handle Split Bill automatic Debt/Receivable creation
                if ($this->isSplitBill && ! empty($this->splitFriends)) {
                    $friends = array_filter(array_map('trim', explode(',', $this->splitFriends)));
                    $remainingForFriends = $this->amount - $personal;
                    $perFriend = count($friends) > 0 ? $remainingForFriends / count($friends) : 0;

                    foreach ($friends as $friend) {
                        $parts = explode(':', $friend);
                        $name = trim($parts[0]);
                        $friendAmt = isset($parts[1]) ? (float) trim($parts[1]) : $perFriend;

                        DebtReceivable::create([
                            'user_id' => $userId,
                            'source_transaction_id' => $tx->id,
                            'type' => 'receivable',
                            'contact_name' => $name,
                            'total_amount' => $friendAmt,
                            'remaining_amount' => $friendAmt,
                            'due_date' => Carbon::parse($this->transactionDate)->addDays(7),
                            'status' => 'active',
                            'note' => "Talangan {$tx->note}",
                        ]);
                    }
                }
            }
        });

        $this->close();
        $this->dispatch('transaction-saved');
    }

    public function render()
    {
        $wallets = auth()->user() ? auth()->user()->wallets()->where('is_active', true)->get() : collect();
        $categories = auth()->user() ? auth()->user()->categories()->where('type', $this->type === 'transfer' ? 'expense' : $this->type)->get() : collect();

        return view('livewire.transaction-modal', compact('wallets', 'categories'));
    }
}
