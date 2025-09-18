<?php

namespace Database\Seeders;

use App\Models\User;
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
                'middle_name' => 'Dela',
                'last_name' => 'Cruz',
                'phone' => '0923432345432',
                'address' => 'Bulusan, Sorsogon',
                'dob' => '1998-09-20',
                'password' => Hash::make('juan12345'),
                'role' => 'seller',
            ],
        );
        User::firstOrCreate(
            ['email' => 'maria@gmail.com'],
            [
                'first_name' => 'Maria',
                'middle_name' => 'O',
                'last_name' => 'Navarro',
                'phone' => '09703427389',
                'address' => 'Matnog, Sorsogon',
                'dob' => '1992-11-30',
                'password' => Hash::make('maria12345'),
                'role' => 'seller',
            ]
        );
    }
}
