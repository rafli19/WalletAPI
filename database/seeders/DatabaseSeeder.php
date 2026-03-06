<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'     => 'Test User',
            'username' => 'testuser',
            'email'    => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        User::factory(10)->create();
    }
}