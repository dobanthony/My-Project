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
                'first_name' => 'Seller',
                'middle_name' => 'Y',
                'last_name' => 'Dizon',
                'phone' => '09324345654',
                'address' => 'Bulusan, Sorsogon',
                'dob' => '1998-09-20',
                'password' => Hash::make('seller12345'),
                'role' => 'seller',
            ],
        );
    }
}
