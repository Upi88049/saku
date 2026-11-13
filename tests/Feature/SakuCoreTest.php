<?php

namespace Tests\Feature;

use App\Livewire\BudgetManager;
use App\Livewire\DebtManager;
use App\Livewire\QuickAddBar;
use App\Livewire\WalletManager;
use App\Models\User;
use App\Services\AutoProvisioningService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SakuCoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Saku')
            ->assertSee('Safe-to-Spend')
            ->assertSee('PAKET STARTER')
            ->assertSee('PAKET PRO');
    }

    public function test_user_registration_auto_provisions_default_wallets_and_categories(): void
    {
        $user = User::factory()->create([
            'email' => 'baru@saku.id',
            'name' => 'Pengguna Baru',
        ]);

        app(AutoProvisioningService::class)->provision($user);

        $this->assertEquals(3, $user->wallets()->count());
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'Bank Utama',
        ]);
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'name' => 'Kas Tunai',
        ]);

        $this->assertTrue($user->categories()->count() > 0);
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Makan & Minum',
            'is_ghost_watch' => true,
        ]);
    }

    public function test_dashboard_displays_safe_to_spend_and_wallets(): void
    {
        $user = User::factory()->create(['name' => 'Dimas']);
        app(AutoProvisioningService::class)->provision($user);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200)
            ->assertSee('Ringkasan Keuangan')
            ->assertSee('Safe-to-Spend Hari Ini')
            ->assertSee('Bank Utama');
    }

    public function test_quick_add_bar_records_expense(): void
    {
        $user = User::factory()->create();
        app(AutoProvisioningService::class)->provision($user);

        $wallet = $user->wallets()->firstWhere('name', 'GoPay');
        $wallet->update(['current_balance' => 100000]);

        $this->actingAs($user);

        Livewire::test(QuickAddBar::class)
            ->set('query', 'Kopi kenangan 24rb gopay')
            ->call('save')
            ->assertDispatched('transaction-saved');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 24000,
            'type' => 'expense',
        ]);

        $this->assertEquals(76000, $wallet->fresh()->current_balance);
    }

    public function test_wallet_manager_can_create_wallet_and_transfer_funds(): void
    {
        $user = User::factory()->create(['plan_tier' => 'pro']);
        app(AutoProvisioningService::class)->provision($user);

        $w1 = $user->wallets()->firstWhere('name', 'Bank Utama');
        $w2 = $user->wallets()->firstWhere('name', 'Kas Tunai');

        $w1->update(['current_balance' => 500000]);
        $w2->update(['current_balance' => 100000]);

        $this->actingAs($user);

        // Test Transfer
        Livewire::test(WalletManager::class)
            ->set('fromWalletId', $w1->id)
            ->set('toWalletId', $w2->id)
            ->set('transferAmount', 150000)
            ->call('executeTransfer')
            ->assertDispatched('wallet-updated');

        $this->assertEquals(350000, $w1->fresh()->current_balance);
        $this->assertEquals(250000, $w2->fresh()->current_balance);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'type' => 'transfer',
            'amount' => 150000,
        ]);
    }

    public function test_debt_manager_can_record_debt_and_payment(): void
    {
        $user = User::factory()->create();
        app(AutoProvisioningService::class)->provision($user);

        $wallet = $user->wallets()->first();
        $wallet->update(['current_balance' => 200000]);

        $this->actingAs($user);

        // Add debt
        Livewire::test(DebtManager::class)
            ->set('formType', 'receivable')
            ->set('contactName', 'Andi')
            ->set('contactPhone', '081298765432')
            ->set('amount', 50000)
            ->set('note', 'Talangan makan')
            ->call('saveDebt')
            ->assertDispatched('debt-updated');

        $debt = $user->debtReceivables()->firstWhere('contact_name', 'Andi');
        $this->assertNotNull($debt);
        $this->assertEquals(50000, $debt->remaining_amount);

        // Record payment
        Livewire::test(DebtManager::class)
            ->set('selectedDebtId', $debt->id)
            ->set('paymentAmount', 50000)
            ->set('paymentWalletId', $wallet->id)
            ->set('paymentDate', Carbon::today()->format('Y-m-d'))
            ->call('savePayment')
            ->assertDispatched('debt-updated');

        $this->assertEquals(0, $debt->fresh()->remaining_amount);
        $this->assertEquals('settled', $debt->fresh()->status);
        $this->assertEquals(250000, $wallet->fresh()->current_balance);
    }

    public function test_budget_manager_can_set_monthly_limit(): void
    {
        $user = User::factory()->create();
        app(AutoProvisioningService::class)->provision($user);

        $cat = $user->categories()->firstWhere('name', 'Makan & Minum');

        $this->actingAs($user);

        Livewire::test(BudgetManager::class)
            ->set('selectedCategoryId', $cat->id)
            ->set('monthlyLimit', 1500000)
            ->call('saveBudget')
            ->assertDispatched('budget-updated');

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'monthly_limit' => 1500000,
        ]);
    }
}
