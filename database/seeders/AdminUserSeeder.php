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
            ['email' => 'admin@gmail.com'],
            [
                'first_name' => 'Nonito',
                'middle_name' => 'T.',
                'last_name' => 'Guirerro',
                'phone' => '09343234567',
                'address' => 'Sorsogon City, Sorsogon',
                'dob' => '1996-11-10',
                'password' => Hash::make('nonito12345'),
                'role' => 'admin',
            ]
        );
    }
}
