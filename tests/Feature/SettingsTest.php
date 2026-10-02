<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_cashier_can_open_account_settings(): void
    {
        foreach (['admin', 'cashier'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('settings.index'))
                ->assertOk()
                ->assertSee('Ganti kata sandi')
                ->assertSee('Kata sandi saat ini')
                ->assertSee('Kata sandi baru')
                ->assertSee(route('password.update'));

            $dashboardRoute = $role === 'admin' ? 'admin.index' : 'kasir.index';

            $this->get(route($dashboardRoute))
                ->assertOk()
                ->assertSee('Pengaturan akun')
                ->assertSee(route('settings.index'))
                ->assertSee(route('logout'))
                ->assertSee('Keluar');
        }
    }

    public function test_guest_is_redirected_to_login_from_account_settings(): void
    {
        $this->get(route('settings.index'))
            ->assertRedirect(route('login'));
    }
}
