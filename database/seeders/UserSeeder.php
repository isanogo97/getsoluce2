<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name'     => 'Admin RH',
            'email'    => 'admin@acme.local',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }
}
