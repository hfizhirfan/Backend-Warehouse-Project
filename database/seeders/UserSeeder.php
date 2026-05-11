<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('123456'),
            'role' => 'super_admin'
        ]);

        User::create([
            'name' => 'Admin Brand A',
            'email' => 'brand@mail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'brand_id' => 1
        ]);

        User::create([
            'name' => 'Guest Brand A',
            'email' => 'guest@mail.com',
            'password' => Hash::make('123456'),
            'role' => 'guest',
            'brand_id' => 1
        ]);
    }
}
