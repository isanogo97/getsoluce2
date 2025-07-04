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
            EntrepriseSeeder::class,
            UserSeeder::class,
            AbsenceSeeder::class,
            CongeSeeder::class,
            NoteDeFraisSeeder::class,
            CompteSeeder::class,
            AvanceSeeder::class,
        ]);
    }
}
