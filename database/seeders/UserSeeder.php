<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(
            ['email' => 'admin@hmis.com'],
            [
                'name'     => 'Administrator',
                'email'    => 'admin@hmis.com',
                'password' => Hash::make('Admin@12345'),
                'role'     => 'admin',
            ]
        );

        // Regular user account
        User::updateOrCreate(
            ['email' => 'user@hmis.com'],
            [
                'name'     => 'HMIS User',
                'email'    => 'user@hmis.com',
                'password' => Hash::make('User@12345'),
                'role'     => 'user',
            ]
        );
    }
}
