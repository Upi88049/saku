<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AutoProvisioningService
{
    /**
     * Provision default wallets and categories for a newly created user.
     */
    public function provision(User $user): void
    {
        DB::transaction(function () use ($user) {
            if ($user->wallets()->count() === 0) {
                Wallet::insert([
                    [
                        'user_id' => $user->id,
                        'name' => 'Bank Utama',
                        'type' => 'bank',
                        'current_balance' => 0,
                        'is_excluded_from_sts' => false,
                        'account_number' => null,
                        'color' => '#4B3FCB',
                        'icon' => 'bank',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Kas Tunai',
                        'type' => 'cash',
                        'current_balance' => 0,
                        'is_excluded_from_sts' => false,
                        'account_number' => null,
                        'color' => '#12A07A',
                        'icon' => 'cash',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'GoPay',
                        'type' => 'ewallet',
                        'current_balance' => 0,
                        'is_excluded_from_sts' => false,
                        'account_number' => null,
                        'color' => '#00AED6',
                        'icon' => 'ewallet',
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

            if ($user->categories()->count() === 0) {
                Category::insert([
                    [
                        'user_id' => $user->id,
                        'name' => 'Makan & Minum',
                        'type' => 'expense',
                        'icon' => 'coffee',
                        'color' => '#F26B4B',
                        'is_ghost_watch' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Transport',
                        'type' => 'expense',
                        'icon' => 'car',
                        'color' => '#2F6BFF',
                        'is_ghost_watch' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Langganan',
                        'type' => 'expense',
                        'icon' => 'receipt',
                        'color' => '#E5483F',
                        'is_ghost_watch' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Belanja & Hiburan',
                        'type' => 'expense',
                        'icon' => 'shopping-bag',
                        'color' => '#6A3FC4',
                        'is_ghost_watch' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Biaya Admin & Transfer',
                        'type' => 'expense',
                        'icon' => 'swap',
                        'color' => '#5B5F80',
                        'is_ghost_watch' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Gaji',
                        'type' => 'income',
                        'icon' => 'wallet',
                        'color' => '#12A07A',
                        'is_ghost_watch' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'user_id' => $user->id,
                        'name' => 'Bonus & Freelance',
                        'type' => 'income',
                        'icon' => 'trending-up',
                        'color' => '#0B7F63',
                        'is_ghost_watch' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }
        });
    }
}
