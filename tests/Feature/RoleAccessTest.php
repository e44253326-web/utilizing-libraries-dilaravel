<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_is_denied_admin_and_cashier_pages(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'customer']));

        $this->get(route('admin.index'))->assertForbidden();
        $this->get(route('kasir.index'))->assertForbidden();
    }

    public function test_cashier_can_use_cashier_pages_but_not_admin_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));

        $this->get(route('kasir.index'))->assertOk();
        $this->get(route('admin.index'))->assertForbidden();
    }

    public function test_admin_can_use_admin_page_but_not_cashier_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('admin.index'))->assertOk();
        $this->get(route('kasir.index'))->assertForbidden();
    }

    public function test_dashboard_redirects_each_staff_role_to_its_own_page(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->get(route('dashboard'))
            ->assertRedirect(route('kasir.index'));

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.index'));
    }

    public function test_demo_admin_seeder_replaces_the_old_account_with_configured_admin_account(): void
    {
        config([
            'demo_accounts.admin.name' => 'Dian',
            'demo_accounts.admin.email' => 'dianname@gmail.com',
            'demo_accounts.admin.password' => 'admin-test-password',
        ]);

        User::factory()->create([
            'email' => 'admin.demo@tokodianname.test',
            'role' => 'admin',
        ]);

        $this->seed('Database\\Seeders\\DemoAdminSeeder');

        $admin = User::query()->where('email', 'dianname@gmail.com')->firstOrFail();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseMissing('users', ['email' => 'admin.demo@tokodianname.test']);
        $this->assertSame('Dian', $admin->name);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('admin-test-password', $admin->password));
        $this->actingAs($admin)->get(route('admin.index'))->assertOk();
        $this->get(route('kasir.index'))->assertForbidden();
    }

    public function test_database_seeder_creates_both_staff_accounts(): void
    {
        config([
            'demo_accounts.admin.email' => 'admin@example.test',
            'demo_accounts.admin.password' => 'admin-test-password',
            'demo_accounts.cashier.email' => 'cashier@example.test',
            'demo_accounts.cashier.password' => 'cashier-test-password',
        ]);

        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'cashier@example.test',
            'role' => 'cashier',
        ]);
        $this->assertDatabaseCount('users', 2);
    }
}
