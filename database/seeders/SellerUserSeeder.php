<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SellerUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'juan@gmail.com'],
            [
                'first_name' => 'Juan',
                'password' => Hash::make('juan12345'), // default password
                'role' => 'seller',
            ],
        );
        User::firstOrCreate(
            ['email' => 'maria@gmail.com'],
            [
                'first_name' => 'Maria',
                'password' => Hash::make('maria12345'),
                'role' => 'seller',
            ]
        );
    }
}
