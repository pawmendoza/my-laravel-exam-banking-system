<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\LowBalanceNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_updates_the_balance_once_and_records_the_transaction(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-123456',
            'balance' => 5000.00,
            'minimum_balance' => 100.00,
        ]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'type' => 'deposit',
            'amount' => 250.00,
            'notes' => 'Initial deposit',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 5250.00,
        ]);
        $this->assertDatabaseHas('transactions', [
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 250.00,
            'notes' => 'Initial deposit',
        ]);
    }

    public function test_withdrawal_below_minimum_balance_is_rejected_without_saving(): void
    {
        $user = User::factory()->create();
        $account = Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-654321',
            'balance' => 5000.00,
            'minimum_balance' => 100.00,
        ]);

        $response = $this->actingAs($user)
            ->from(route('transactions.create'))
            ->post(route('transactions.store'), [
                'type' => 'withdrawal',
                'amount' => 5000.00,
            ]);

        $response->assertRedirect(route('transactions.create'));
        $response->assertSessionHasErrors('amount');
        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'balance' => 5000.00,
        ]);
        $this->assertSame(0, Transaction::count());
        $this->assertDatabaseHas('notifications', [
            'type' => LowBalanceNotification::class,
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
        ]);

        $notification = $user->notifications()->firstOrFail();
        $this->assertSame('withdrawal', $notification->data['transaction_type']);
        $this->assertEquals(5000.0, $notification->data['attempted_amount']);
        $this->assertEquals(5000.0, $notification->data['current_balance']);
        $this->assertEquals(100.0, $notification->data['minimum_balance']);
    }

    public function test_dashboard_and_transaction_pages_render_for_a_user_without_an_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('dashboard'))->assertOk();
        $this->get(route('transactions.index'))->assertOk();
        $this->get(route('transactions.create'))->assertOk();
    }

    public function test_balance_api_returns_the_existing_endpoint_and_balance_shape(): void
    {
        $user = User::factory()->create();
        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-987654',
            'balance' => 1234.50,
            'minimum_balance' => 100.00,
        ]);

        $this->getJson('/api/balance')
            ->assertOk()
            ->assertJson([
                'balance' => '1234.50',
                'minimum_balance' => '100.00',
            ]);
    }

    public function test_dashboard_stats_api_returns_statistics(): void
    {
        $this->getJson('/api/dashboard/stats')
            ->assertOk()
            ->assertExactJson([
                'total_accounts' => 0,
                'total_money' => 0,
                'total_transactions_today' => 0,
            ]);
    }
}
