<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DemoAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Akun demo admin hanya boleh dibuat di lingkungan lokal atau testing.');
        }

        $account = config('demo_accounts.admin');
        $email = $account['email'] ?? null;
        $password = $account['password'] ?? null;

        if (! is_string($email) || trim($email) === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException('Email dan kata sandi demo admin harus diatur di file .env.');
        }

        User::query()->where('email', 'admin.demo@tokodianname.test')->delete();

        User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $account['name'],
                'email_verified_at' => now(),
                'password' => $password,
                'role' => 'admin',
            ],
        );

        if ($this->command !== null) {
            $this->command->info('Email demo: '.$email);
        }
    }
}
