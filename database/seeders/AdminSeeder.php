<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'       => 'Administrateur',
                'email'      => 'admin@gmail.com',
                'login_code' => 'ADM-0001',
                'password'   => Hash::make('azertyui'),
                'role'       => 'admin',
            ]
        );
    }
}
