<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Enterprise;

class EnterpriseSeeder extends Seeder
{
    public function run(): void
    {
        Enterprise::truncate();
        Enterprise::create([
            'name' => 'Acme Corp',
        ]);
    }
}
