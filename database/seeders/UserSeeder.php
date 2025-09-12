<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['John', 'A', 'Tan', '09123456701', 'Bulan, Sorsogon', '1995-01-10'],
            ['Jane', 'B', 'Smith', '09123456702', 'Bulusan, Sorsogon', '1993-02-15'],
            ['Mark', 'C', 'Tan', '09123456703', 'Matnog, Sorsogon', '1990-03-20'],
            ['Anna', 'D', 'Lopez', '09123456704', 'Gubat, Sorsogon', '1992-04-05'],
            ['Paul', 'E', 'Garcia', '09123456705', 'Barcelona, Sorsogon', '1988-05-12'],
            ['Maria', 'F', 'Reyes', '09123456706', 'Irosin, Sorsogon', '1991-06-18'],
            ['James', 'G', 'Cruz', '09123456707', 'Casiguran, Sorsogon', '1989-07-22'],
            ['Ella', 'H', 'Santos', '09123456708', 'Pilar, Sorsogon', '1994-08-30'],
            ['Leo', 'I', 'Valdez', '09123456709', 'Bulusan, Sorsogon', '1990-09-25'],
            ['Sofia', 'J', 'Martinez', '09123456710', 'Bulan, Sorsogon', '1993-10-15'],
        ];

        foreach ($users as $index => $user) {
            User::firstOrCreate(
                ['email' => 'user'.($index + 1).'@gmail.com'],
                [
                    'first_name' => $user[0],
                    'middle_name' => $user[1],
                    'last_name' => $user[2],
                    'phone' => $user[3],
                    'address' => $user[4],
                    'dob' => $user[5],
                    'password' => Hash::make('user'.($index + 1).'12345'),
                    'role' => 'user',
                ]
            );
        }
    }
}
