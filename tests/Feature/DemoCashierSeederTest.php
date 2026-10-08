<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DemoCashierSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_cashier_can_log_in_and_access_the_cashier_page_after_seeding(): void
    {
        config([
            'demo_accounts.cashier.name' => 'Kasir Dian',
            'demo_accounts.cashier.email' => 'dian@toko.test',
            'demo_accounts.cashier.password' => 'cashier-test-password',
        ]);

        User::factory()->create([
            'email' => 'kasir.demo@tokodianname.test',
            'role' => 'cashier',
        ]);

        $this->seed('Database\\Seeders\\DemoCashierSeeder');
        $this->seed('Database\\Seeders\\DemoCashierSeeder');

        $user = User::query()->where('email', 'dian@toko.test')->firstOrFail();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', ['email' => 'kasir.demo@tokodianname.test']);
        $this->assertSame('Kasir Dian', $user->name);
        $this->assertSame('cashier', $user->role);
        $this->assertTrue(Hash::check('cashier-test-password', $user->password));

        $this->post('/login', [
            'email' => 'dian@toko.test',
            'password' => 'cashier-test-password',
        ])->assertRedirect(route('kasir.index', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->get(route('kasir.index'))->assertOk();
        $this->get(route('admin.index'))->assertForbidden();
    }
}
