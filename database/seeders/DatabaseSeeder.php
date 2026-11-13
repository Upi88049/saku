<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\DebtReceivable;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'dimas@saku.id'],
            [
                'name' => 'Dimas',
                'password' => Hash::make('password'),
                'plan_tier' => 'pro',
                'currency' => 'IDR',
                'budget_start_day' => 28,
                'payment_info' => 'BCA 1234567890 a.n Dimas / GoPay 081234567890',
            ]
        );

        // Wallets
        $bank = Wallet::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Bank Utama'],
            ['type' => 'bank', 'current_balance' => 6250000, 'color' => '#4B3FCB', 'icon' => 'bank', 'is_excluded_from_sts' => false]
        );
        $darurat = Wallet::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Dana Darurat'],
            ['type' => 'savings', 'current_balance' => 5400000, 'color' => '#12A07A', 'icon' => 'savings', 'is_excluded_from_sts' => true]
        );
        $gopay = Wallet::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'GoPay'],
            ['type' => 'ewallet', 'current_balance' => 480000, 'color' => '#00AED6', 'icon' => 'ewallet', 'is_excluded_from_sts' => false]
        );
        $tunai = Wallet::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Tunai'],
            ['type' => 'cash', 'current_balance' => 350000, 'color' => '#F5A524', 'icon' => 'cash', 'is_excluded_from_sts' => false]
        );

        // Categories
        $catFood = Category::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Makan & Minum'],
            ['type' => 'expense', 'icon' => 'coffee', 'color' => '#F26B4B', 'is_ghost_watch' => true]
        );
        $catTransport = Category::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Transport'],
            ['type' => 'expense', 'icon' => 'car', 'color' => '#2352B8', 'is_ghost_watch' => false]
        );
        $catEntertainment = Category::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Hiburan'],
            ['type' => 'expense', 'icon' => 'film', 'color' => '#6A3FC4', 'is_ghost_watch' => false]
        );
        $catSub = Category::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Langganan'],
            ['type' => 'expense', 'icon' => 'receipt', 'color' => '#E5483F', 'is_ghost_watch' => false]
        );
        $catAdmin = Category::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Biaya Admin & Transfer'],
            ['type' => 'expense', 'icon' => 'swap', 'color' => '#5B5F80', 'is_ghost_watch' => true]
        );
        $catSalary = Category::updateOrCreate(
            ['user_id' => $user->id, 'name' => 'Gaji'],
            ['type' => 'income', 'icon' => 'wallet', 'color' => '#12A07A', 'is_ghost_watch' => false]
        );

        // Budgets for current month
        $now = Carbon::now();
        Budget::updateOrCreate(
            ['user_id' => $user->id, 'category_id' => $catFood->id, 'month' => $now->month, 'year' => $now->year],
            ['monthly_limit' => 2000000]
        );
        Budget::updateOrCreate(
            ['user_id' => $user->id, 'category_id' => $catTransport->id, 'month' => $now->month, 'year' => $now->year],
            ['monthly_limit' => 600000]
        );
        Budget::updateOrCreate(
            ['user_id' => $user->id, 'category_id' => $catEntertainment->id, 'month' => $now->month, 'year' => $now->year],
            ['monthly_limit' => 600000]
        );
        Budget::updateOrCreate(
            ['user_id' => $user->id, 'category_id' => $catSub->id, 'month' => $now->month, 'year' => $now->year],
            ['monthly_limit' => 300000]
        );

        // Debts & Receivables
        DebtReceivable::updateOrCreate(
            ['user_id' => $user->id, 'contact_name' => 'Sari'],
            [
                'type' => 'receivable',
                'contact_phone' => '081234567890',
                'total_amount' => 150000,
                'remaining_amount' => 150000,
                'due_date' => Carbon::now()->subDays(3),
                'status' => 'active',
                'note' => 'Pinjaman',
            ]
        );
        DebtReceivable::updateOrCreate(
            ['user_id' => $user->id, 'contact_name' => 'Andi'],
            [
                'type' => 'receivable',
                'contact_phone' => '081298765432',
                'total_amount' => 38000,
                'remaining_amount' => 38000,
                'due_date' => Carbon::now()->addDays(2),
                'status' => 'active',
                'note' => 'Patungan bakso',
            ]
        );
        DebtReceivable::updateOrCreate(
            ['user_id' => $user->id, 'contact_name' => 'Budi'],
            [
                'type' => 'receivable',
                'contact_phone' => '081345678901',
                'total_amount' => 197000,
                'remaining_amount' => 197000,
                'due_date' => Carbon::now()->addDays(12),
                'status' => 'active',
                'note' => 'Tiket konser',
            ]
        );
        DebtReceivable::updateOrCreate(
            ['user_id' => $user->id, 'contact_name' => 'Rekan Kantor'],
            [
                'type' => 'debt',
                'contact_phone' => '08111223344',
                'total_amount' => 1200000,
                'remaining_amount' => 1200000,
                'due_date' => Carbon::now()->addMonths(6),
                'status' => 'active',
                'note' => 'Cicilan laptop rekan',
            ]
        );

        // Recent & recurring transactions
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Netflix', 'is_recurring' => true],
            [
                'wallet_id' => $gopay->id,
                'category_id' => $catSub->id,
                'type' => 'expense',
                'amount' => 54000,
                'transaction_date' => Carbon::now()->addDay(),
                'recurring_interval' => 'monthly',
            ]
        );
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Listrik Token PLN', 'is_recurring' => true],
            [
                'wallet_id' => $bank->id,
                'category_id' => $catSub->id,
                'type' => 'expense',
                'amount' => 196000,
                'transaction_date' => Carbon::now()->addDays(3),
                'recurring_interval' => 'monthly',
            ]
        );
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Cicilan HP', 'is_recurring' => true],
            [
                'wallet_id' => $bank->id,
                'category_id' => $catSub->id,
                'type' => 'expense',
                'amount' => 250000,
                'transaction_date' => Carbon::now()->addDays(7),
                'recurring_interval' => 'monthly',
            ]
        );

        // Recent expenses
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Kopi Kenangan', 'transaction_date' => Carbon::today()],
            [
                'wallet_id' => $gopay->id,
                'category_id' => $catFood->id,
                'type' => 'expense',
                'amount' => 24000,
                'is_recurring' => false,
            ]
        );
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Makan siang warteg', 'transaction_date' => Carbon::today()],
            [
                'wallet_id' => $tunai->id,
                'category_id' => $catFood->id,
                'type' => 'expense',
                'amount' => 22000,
                'is_recurring' => false,
            ]
        );
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Grab ke kantor', 'transaction_date' => Carbon::today()],
            [
                'wallet_id' => $gopay->id,
                'category_id' => $catTransport->id,
                'type' => 'expense',
                'amount' => 16000,
                'is_recurring' => false,
            ]
        );
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Split bill bakso', 'transaction_date' => Carbon::yesterday()],
            [
                'wallet_id' => $gopay->id,
                'category_id' => $catFood->id,
                'type' => 'expense',
                'amount' => 76000,
                'personal_amount' => 38000,
                'is_recurring' => false,
            ]
        );
        Transaction::firstOrCreate(
            ['user_id' => $user->id, 'note' => 'Gaji Bulanan', 'type' => 'income'],
            [
                'wallet_id' => $bank->id,
                'category_id' => $catSalary->id,
                'amount' => 8500000,
                'transaction_date' => Carbon::now()->startOfMonth(),
                'is_recurring' => false,
            ]
        );
    }
}
