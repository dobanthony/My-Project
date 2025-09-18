<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'paulcrisfuriofuentes21@gmail.com'],
            [
                'first_name' => 'Paul Cris',
                'middle_name' => 'F.',
                'last_name' => 'Fuentes',
                'phone' => '09234322323',
                'address' => 'San Antonio, Bulusan, Sorsogon',
                'dob' => '2004-05-19',
                'password' => Hash::make('paul12345'),
                'role' => 'user',
            ],
        );

        User::firstOrCreate(
            ['email' => 'lizamaegautane76@gmail.com'],
            [
                'first_name' => 'Liza Mae',
                'middle_name' => 'F.',
                'last_name' => 'Gautane',
                'phone' => '092343345432',
                'address' => 'San Roque, Sta. Magdalena Sorsogon',
                'dob' => '2004-05-26',
                'password' => Hash::make('liza12345'),
                'role' => 'user',
            ],
        );

        User::firstOrCreate(
            ['email' => 'irishgavino05@gmail.com'],
            [
                'first_name' => 'Irish',
                'middle_name' => 'F.',
                'last_name' => 'Gavino',
                'phone' => '092765345432',
                'address' => 'San Antonio, Bulusan, Sorsogon',
                'dob' => '2003-11-05',
                'password' => Hash::make('irish12345'),
                'role' => 'user',
            ],
        );

        User::firstOrCreate(
            ['email' => 'dobanthony38@gmail.com'],
            [
                'first_name' => 'Anthony',
                'middle_name' => 'B.',
                'last_name' => 'Dob',
                'phone' => '09703427389',
                'address' => 'San Isidro Bulan Sorsogon',
                'dob' => '2002-07-10',
                'password' => Hash::make('anthony12345'),
                'role' => 'user',
            ],
        );

    }
}
