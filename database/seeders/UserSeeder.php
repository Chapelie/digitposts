<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur admin
        User::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@digiposts.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Créer quelques utilisateurs de test
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'firstname' => "User",
                'lastname' => "{$i}",
                'email' => "user{$i}@digiposts.com",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
        }
    }
}
