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
            ['John', 'A', 'Doe', '09123456701', 'Sorsogon City', '1995-01-10'],
            ['Jane', 'B', 'Smith', '09123456702', 'Bulusan', '1993-02-15'],
            ['Mark', 'C', 'Tan', '09123456703', 'Matnog', '1990-03-20'],
            ['Anna', 'D', 'Lopez', '09123456704', 'Gubat', '1992-04-05'],
            ['Paul', 'E', 'Garcia', '09123456705', 'Barcelona', '1988-05-12'],
            ['Maria', 'F', 'Reyes', '09123456706', 'Irosin', '1991-06-18'],
            ['James', 'G', 'Cruz', '09123456707', 'Casiguran', '1989-07-22'],
            ['Ella', 'H', 'Santos', '09123456708', 'Pilar', '1994-08-30'],
            ['Leo', 'I', 'Valdez', '09123456709', 'Bulusan', '1990-09-25'],
            ['Sofia', 'J', 'Martinez', '09123456710', 'Sorsogon City', '1993-10-15'],
            ['Carlos', 'K', 'Delgado', '09123456711', 'Magallanes', '1987-11-05'],
            ['Luna', 'L', 'Gomez', '09123456712', 'Gubat', '1995-12-12'],
            ['Antonio', 'M', 'Ramos', '09123456713', 'Matnog', '1992-01-20'],
            ['Isabella', 'N', 'Torres', '09123456714', 'Sorsogon City', '1991-02-28'],
            ['Miguel', 'O', 'Flores', '09123456715', 'Bulusan', '1989-03-18'],
            ['Camila', 'P', 'Navarro', '09123456716', 'Irosin', '1994-04-10'],
            ['Diego', 'Q', 'Lopez', '09123456717', 'Barcelona', '1990-05-25'],
            ['Valeria', 'R', 'Santos', '09123456718', 'Casiguran', '1993-06-30'],
            ['Javier', 'S', 'Mendoza', '09123456719', 'Pilar', '1988-07-15'],
            ['Lucia', 'T', 'Reyes', '09123456720', 'Sorsogon City', '1992-08-22'],
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
