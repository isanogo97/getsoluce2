<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreatorSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'creator@example.com'],
            [
                'name' => 'Project Creator',
                'password' => Hash::make('password'),
                'role' => 'creator',
                'enterprise_id' => null,
            ]
        );
    }
}
