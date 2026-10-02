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
        $this->seed('Database\\Seeders\\DemoCashierSeeder');
        $this->seed('Database\\Seeders\\DemoCashierSeeder');

        $user = User::query()->where('email', 'kasir.demo@tokodianname.test')->firstOrFail();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame('Kasir Demo', $user->name);
        $this->assertSame('cashier', $user->role);
        $this->assertTrue(Hash::check('KasirDian#2026!', $user->password));

        $this->post('/login', [
            'email' => 'kasir.demo@tokodianname.test',
            'password' => 'KasirDian#2026!',
        ])->assertRedirect(route('kasir.index', absolute: false));

        $this->assertAuthenticatedAs($user);
        $this->get(route('kasir.index'))->assertOk();
        $this->get(route('admin.index'))->assertForbidden();
    }
}
