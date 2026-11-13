<?php

namespace App\Livewire;

use App\Models\Budget;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    protected $listeners = [
        'transaction-saved' => '$refresh',
        'wallet-updated' => '$refresh',
        'debt-updated' => '$refresh',
        'budget-updated' => '$refresh',
    ];

    public function render()
    {
        $user = auth()->user();
        $now = Carbon::now();

        // 1. Wallets & Total Balance
        $wallets = $user->wallets()->where('is_active', true)->get();
        $totalBalance = $wallets->where('is_excluded_from_sts', false)->sum('current_balance');

        // 2. Payday Cycle & Days Remaining
        $startDay = $user->budget_start_day ?: 25;
        $cycleStart = $now->copy();
        if ($now->day < $startDay) {
            $cycleStart->subMonthNoOverflow()->day($startDay);
            $cycleEnd = $now->copy()->day($startDay - 1);
        } else {
            $cycleStart->day($startDay);
            $cycleEnd = $now->copy()->addMonthNoOverflow()->day($startDay - 1);
        }
        $remainingDays = max(1, $now->diffInDays($cycleEnd, false) + 1);

        // 3. Budgets & Spent in Current Month
        $budgets = $user->budgets()
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->with('category')
            ->get();

        $budgetSummary = [];
        $totalBudgetLimit = 0;
        $totalBudgetSpent = 0;

        foreach ($budgets as $b) {
            $spent = $user->transactions()
                ->where('category_id', $b->category_id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$cycleStart->toDateString(), $cycleEnd->toDateString()])
                ->sum('amount');

            $pct = $b->monthly_limit > 0 ? round(($spent / $b->monthly_limit) * 100) : 0;
            $totalBudgetLimit += $b->monthly_limit;
            $totalBudgetSpent += $spent;

            $budgetSummary[] = [
                'name' => $b->category->name,
                'used' => $spent,
                'limit' => $b->monthly_limit,
                'pct' => $pct,
                'tag' => $pct > 90 ? 'Hampir habis' : ($pct > 70 ? 'Waspada' : 'Aman'),
                'tag_class' => $pct > 90 ? 'bg-[#FDE1DF] text-[#9E2019]' : ($pct > 70 ? 'bg-[#FFF1CC] text-[#7A5600]' : 'bg-[#DDF5EC] text-[#0B6B52]'),
                'bar_color' => $pct > 90 ? '#D6372F' : ($pct > 70 ? '#E0A100' : '#12A07A'),
            ];
        }

        // 4. Upcoming Recurring Bills in this cycle
        $upcomingBills = $user->transactions()
            ->where('is_recurring', true)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$now->toDateString(), $cycleEnd->toDateString()])
            ->get();
        $totalUpcomingBills = $upcomingBills->sum('amount');

        // 5. Safe-to-Spend Calculation
        // Formula: (Sisa Budget Aktif - Tagihan Rutin Belum Terbayar) / Sisa Hari
        $remainingBudget = max(0, $totalBudgetLimit - $totalBudgetSpent);
        $safeToSpend = $remainingDays > 0 ? max(0, round(($remainingBudget - $totalUpcomingBills) / $remainingDays)) : 0;

        // Today's spending
        $spentToday = $user->transactions()
            ->where('type', 'expense')
            ->whereDate('transaction_date', Carbon::today())
            ->sum('amount');
        $stsRemainingToday = max(0, $safeToSpend - $spentToday);
        $stsPctUsed = $safeToSpend > 0 ? min(100, round(($spentToday / $safeToSpend) * 100)) : 0;

        // 6. Metrics: Income, Expense, Net Flow for this month
        $monthlyIncome = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $monthlyExpense = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->sum('amount');

        $netCashFlow = $monthlyIncome - $monthlyExpense;

        // 7. Debts & Receivables
        $debtsActive = $user->debtReceivables()->where('status', 'active')->get();
        $totalReceivable = $debtsActive->where('type', 'receivable')->sum('remaining_amount');
        $totalDebt = $debtsActive->where('type', 'debt')->sum('remaining_amount');
        $receivableOverdueCount = $debtsActive->where('type', 'receivable')->filter(function ($d) {
            return $d->due_date && Carbon::parse($d->due_date)->isPast();
        })->count();

        // 8. Ghost Expenses (Small repeating transactions under 50k or watched categories)
        $ghostExpenses = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', $now->month)
            ->whereYear('transaction_date', $now->year)
            ->where(function ($q) {
                $q->where('amount', '<=', 50000)
                    ->orWhereHas('category', fn ($c) => $c->where('is_ghost_watch', true));
            })
            ->select('note', 'category_id', \DB::raw('count(*) as count'), \DB::raw('sum(amount) as total'))
            ->groupBy('note', 'category_id')
            ->having('count', '>=', 2)
            ->orderByDesc('total')
            ->take(4)
            ->get();
        $totalGhost = $ghostExpenses->sum('total');
        $ghostPct = $monthlyExpense > 0 ? round(($totalGhost / $monthlyExpense) * 100) : 0;

        // 9. Recent Transactions (last 8)
        $recentTransactions = $user->transactions()
            ->with(['wallet', 'category', 'toWallet'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(8)
            ->get();

        // 10. Receivables Needing Reminder
        $receivablesToRemind = $user->debtReceivables()
            ->where('type', 'receivable')
            ->where('status', 'active')
            ->orderBy('due_date')
            ->take(4)
            ->get();

        return view('livewire.dashboard', compact(
            'wallets',
            'totalBalance',
            'cycleStart',
            'cycleEnd',
            'remainingDays',
            'safeToSpend',
            'spentToday',
            'stsRemainingToday',
            'stsPctUsed',
            'remainingBudget',
            'totalUpcomingBills',
            'monthlyIncome',
            'monthlyExpense',
            'netCashFlow',
            'totalReceivable',
            'totalDebt',
            'receivableOverdueCount',
            'budgetSummary',
            'ghostExpenses',
            'totalGhost',
            'ghostPct',
            'recentTransactions',
            'upcomingBills',
            'receivablesToRemind'
        ));
    }
}
