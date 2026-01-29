<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin (rôle fictif pour accès admin)
        User::updateOrCreate(
            ['email' => 'admin@digiposts.com'],
            [
                'firstname' => 'Admin',
                'lastname' => 'DigitPosts',
                'email' => 'admin@digiposts.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => true,
                'role' => 'admin',
            ]
        );

        // Utilisateurs de test (rôle user)
        for ($i = 1; $i <= 5; $i++) {
            User::updateOrCreate(
                ['email' => "user{$i}@digiposts.com"],
                [
                    'firstname' => "User",
                    'lastname' => "{$i}",
                    'email' => "user{$i}@digiposts.com",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_admin' => false,
                    'role' => 'user',
                ]
            );
        }
    }
}
