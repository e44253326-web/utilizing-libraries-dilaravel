<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_inventory_and_paid_sales_totals_for_today(): void
    {
        $user = User::factory()->create(['name' => 'Admin Demo', 'role' => 'admin']);
        $this->actingAs($user);

        Product::create([
            'name' => 'Susu UHT',
            'price' => 12500,
            'stock' => 3,
        ]);
        Product::create([
            'name' => 'Air Mineral',
            'price' => 5000,
            'stock' => 0,
        ]);
        Product::create([
            'name' => 'Beras',
            'price' => 15000,
            'stock' => 20,
        ]);

        Transaction::create([
            'transaction_number' => 'TRX-20260928-001',
            'user_id' => $user->id,
            'customer_name' => 'Umum',
            'subtotal' => 25000,
            'discount' => 0,
            'grand_total' => 25000,
            'paid_amount' => 25000,
            'change_amount' => 0,
            'payment_method' => 'Tunai',
            'status' => 'paid',
        ]);
        Transaction::create([
            'transaction_number' => 'TRX-20260928-002',
            'user_id' => $user->id,
            'customer_name' => 'Umum',
            'subtotal' => 90000,
            'discount' => 0,
            'grand_total' => 90000,
            'paid_amount' => 0,
            'change_amount' => 0,
            'payment_method' => 'Tunai',
            'status' => 'held',
        ]);

        $response = $this->get(route('admin.index'));

        $response->assertOk()
            ->assertSee('Selamat datang, Admin Demo')
            ->assertSee('TRX-20260928-001')
            ->assertSee('Rp25.000')
            ->assertSee('Susu UHT')
            ->assertSee('Air Mineral')
            ->assertDontSee('TRX-20260928-002');
    }

    public function test_guest_is_redirected_to_login_from_admin_dashboard(): void
    {
        $this->get(route('admin.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'customer']))
            ->get(route('admin.index'))
            ->assertForbidden();
    }

    public function test_dashboard_route_redirects_admin_users_to_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('dashboard'))
            ->assertRedirect(route('admin.index'));
    }
}
