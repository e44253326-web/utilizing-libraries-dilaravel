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

    public function test_demo_admin_seeder_creates_a_separate_admin_account(): void
    {
        $this->seed('Database\\Seeders\\DemoAdminSeeder');

        $admin = User::query()->where('email', 'admin.demo@tokodianname.test')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('AdminDian#2026!', $admin->password));
        $this->actingAs($admin)->get(route('admin.index'))->assertOk();
        $this->get(route('kasir.index'))->assertForbidden();
    }
}
