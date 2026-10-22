<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Account::create([
            'user_id' => $user->id,
            'account_number' => 'ACC-12345',
            'balance' => 5000.00,
            'minimum_balance' => 100.00,
        ]);
    }
}