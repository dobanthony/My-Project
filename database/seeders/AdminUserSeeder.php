<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'anthony10@gmail.com'],
            [
                'name' => 'Anthony Dob',
                'password' => Hash::make('admin'), // default password
                'role' => 'admin',
            ]
        );
    }
}
