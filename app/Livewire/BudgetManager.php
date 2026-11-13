<?php

namespace App\Livewire;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BudgetManager extends Component
{
    public int $selectedMonth;

    public int $selectedYear;

    // Set budget form
    public bool $isFormOpen = false;

    public ?int $selectedCategoryId = null;

    public float $monthlyLimit = 0;

    // Payday Wizard form
    public bool $isWizardOpen = false;

    public float $salaryAmount = 0;

    public ?int $salaryWalletId = null;

    public ?int $savingsWalletId = null;

    public int $needsPct = 50;

    public int $savingsPct = 20;

    public int $wantsPct = 20;

    public int $emergencyPct = 10;

    public function mount(): void
    {
        $this->selectedMonth = Carbon::now()->month;
        $this->selectedYear = Carbon::now()->year;
    }

    public function openSetBudget(?int $categoryId = null): void
    {
        $this->selectedCategoryId = $categoryId;
        if ($categoryId) {
            $existing = auth()->user()->budgets()
                ->where('category_id', $categoryId)
                ->where('month', $this->selectedMonth)
                ->where('year', $this->selectedYear)
                ->first();
            $this->monthlyLimit = $existing ? (float) $existing->monthly_limit : 0;
        } else {
            $first = auth()->user()->categories()->where('type', 'expense')->first();
            $this->selectedCategoryId = $first?->id;
            $this->monthlyLimit = 0;
        }
        $this->isFormOpen = true;
    }

    public function saveBudget(): void
    {
        $this->validate([
            'selectedCategoryId' => 'required|exists:categories,id',
            'monthlyLimit' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'category_id' => $this->selectedCategoryId,
                'month' => $this->selectedMonth,
                'year' => $this->selectedYear,
            ],
            [
                'monthly_limit' => $this->monthlyLimit,
            ]
        );

        $this->isFormOpen = false;
        session()->flash('success', 'Budget kategori berhasil diperbarui.');
        $this->dispatch('budget-updated');
    }

    public function openWizard(): void
    {
        $wallets = auth()->user()->wallets()->where('is_active', true)->get();
        $this->salaryWalletId = $wallets->firstWhere('type', 'bank')?->id ?? $wallets->first()?->id;
        $this->savingsWalletId = $wallets->firstWhere('type', 'savings')?->id ?? $wallets->last()?->id;
        $this->salaryAmount = 0;
        $this->isWizardOpen = true;
    }

    public function executeWizard(): void
    {
        $this->validate([
            'salaryAmount' => 'required|numeric|min:100000',
            'salaryWalletId' => 'required|exists:wallets,id',
        ]);

        DB::transaction(function () {
            $user = auth()->user();

            // 1. Catat Gaji Masuk
            $catSalary = $user->categories()->where('type', 'income')->firstWhere('name', 'Gaji')
                ?? $user->categories()->where('type', 'income')->first();

            $salaryWallet = Wallet::find($this->salaryWalletId);

            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $salaryWallet->id,
                'category_id' => $catSalary?->id,
                'type' => 'income',
                'amount' => $this->salaryAmount,
                'transaction_date' => Carbon::today(),
                'note' => 'Gaji Bulanan (Payday Wizard)',
            ]);

            $salaryWallet->increment('current_balance', $this->salaryAmount);

            // 2. Transfer otomatis ke Tabungan jika ada
            if ($this->savingsWalletId && $this->savingsWalletId != $this->salaryWalletId) {
                $savingsAmount = round($this->salaryAmount * ($this->savingsPct / 100));
                if ($savingsAmount > 0) {
                    $savingsWallet = Wallet::find($this->savingsWalletId);

                    Transaction::create([
                        'user_id' => $user->id,
                        'wallet_id' => $salaryWallet->id,
                        'to_wallet_id' => $savingsWallet->id,
                        'type' => 'transfer',
                        'amount' => $savingsAmount,
                        'transaction_date' => Carbon::today(),
                        'note' => "Alokasi Tabungan {$this->savingsPct}% (Payday Wizard)",
                    ]);

                    $salaryWallet->decrement('current_balance', $savingsAmount);
                    $savingsWallet->increment('current_balance', $savingsAmount);
                }
            }
        });

        $this->isWizardOpen = false;
        session()->flash('success', 'Ritual Gajian berhasil diproses! Saldo dan alokasi telah diperbarui.');
        $this->dispatch('budget-updated');
    }

    public function render()
    {
        $user = auth()->user();
        $categories = $user->categories()->where('type', 'expense')->get();

        $budgets = $user->budgets()
            ->where('month', $this->selectedMonth)
            ->where('year', $this->selectedYear)
            ->with('category')
            ->get()
            ->keyBy('category_id');

        $budgetRows = [];
        $totalLimit = 0;
        $totalUsed = 0;

        foreach ($categories as $cat) {
            $limit = isset($budgets[$cat->id]) ? (float) $budgets[$cat->id]->monthly_limit : 0;
            $used = (float) $user->transactions()
                ->where('category_id', $cat->id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $this->selectedMonth)
                ->whereYear('transaction_date', $this->selectedYear)
                ->sum('amount');

            $pct = $limit > 0 ? round(($used / $limit) * 100) : 0;
            $totalLimit += $limit;
            $totalUsed += $used;

            $budgetRows[] = [
                'category_id' => $cat->id,
                'name' => $cat->name,
                'limit' => $limit,
                'used' => $used,
                'remaining' => max(0, $limit - $used),
                'pct' => $pct,
                'tag' => $pct > 90 ? 'Hampir habis' : ($pct > 70 ? 'Waspada' : 'Aman'),
                'tag_class' => $pct > 90 ? 'bg-[#FDE1DF] text-[#9E2019]' : ($pct > 70 ? 'bg-[#FFF1CC] text-[#7A5600]' : 'bg-[#DDF5EC] text-[#0B6B52]'),
                'bar_color' => $pct > 90 ? '#D6372F' : ($pct > 70 ? '#E0A100' : '#12A07A'),
            ];
        }

        $wallets = $user->wallets()->where('is_active', true)->get();

        return view('livewire.budget-manager', compact('budgetRows', 'totalLimit', 'totalUsed', 'categories', 'wallets'));
    }
}
