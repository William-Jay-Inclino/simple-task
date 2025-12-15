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
            [
                'name' => 'Charity Tura',
                'email' => 'charity@goteam.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'William Jay Inclino',
                'email' => 'jay@goteam.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'John Davis',
                'email' => 'john@goteam.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emily Chen',
                'email' => 'emily@goteam.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael@goteam.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::create($userData);
        }
    }
}
