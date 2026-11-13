<?php

use App\Livewire\BudgetManager;
use App\Livewire\Dashboard;
use App\Livewire\DebtManager;
use App\Livewire\SettingsManager;
use App\Livewire\TransactionManager;
use App\Livewire\WalletManager;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/transactions', TransactionManager::class)->name('transactions');
    Route::get('/wallets', WalletManager::class)->name('wallets');
    Route::get('/debts', DebtManager::class)->name('debts');
    Route::get('/budgets', BudgetManager::class)->name('budgets');
    Route::get('/settings', SettingsManager::class)->name('settings');

    Route::view('profile', 'profile')->name('profile');
});

require __DIR__.'/auth.php';
