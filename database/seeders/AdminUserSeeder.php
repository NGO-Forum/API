<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Borey',
                'email' => 'borey@ngoforum.org.kh',
                'password' => Hash::make('borey2025@NGOF'),
                'is_admin' => true,
            ],
            [
                'name' => 'Vocheth',
                'email' => 'vicheth@ngoforum.org.kh',
                'password' => Hash::make('vicheth2025@NGOF'),
                'is_admin' => true,
            ],
            [
                'name' => 'Mengseu',
                'email' => 'mengseu@ngoforum.org.kh',
                'password' => Hash::make('mengseu2025@NGOF'),
                'is_admin' => true,
            ],
            [
                'name' => 'Bunna',
                'email' => 'bunna@ngoforum.org.kh',
                'password' => Hash::make('bunna2025@NGOF'),
                'is_admin' => true,
            ],
            [
                'name' => 'Admin',
                'email' => 'info@ngoforum.org.kh',
                'password' => Hash::make('info2025@NGOF'),
                'is_admin' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
