<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Lance tous les seeders.
     */
    public function run(): void
    {
        $this->call([
            EnterpriseSeeder::class,
            UserSeeder::class,
            AbsenceSeeder::class,
            // Additional seeders can be added here
        ]);
    }
}
