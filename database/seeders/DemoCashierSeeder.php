<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoCashierSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Akun demo kasir hanya boleh dibuat di lingkungan lokal atau testing.');
        }

        $email = 'kasir.demo@tokodianname.test';
        $password = 'KasirDian#2026!';

        $user = User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Kasir Demo',
                'email_verified_at' => now(),
                'password' => Hash::make($password),
                'role' => 'cashier',
            ],
        );
        $user->forceFill([
            'role' => 'cashier',
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        if ($this->command !== null) {
            $this->command->info('Email demo: '.$email);

            if (Hash::check($password, $user->password)) {
                $this->command->info('Kata sandi demo: '.$password);
            } else {
                $this->command->warn('Akun email tersebut sudah ada dan kata sandinya tidak diubah.');
            }
        }
    }
}
