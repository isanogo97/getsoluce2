// database/seeders/EntrepriseSeeder.php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entreprise;

class EntrepriseSeeder extends Seeder
{
    public function run(): void
    {
        Entreprise::truncate();
        Entreprise::create([
            'nom' => 'Acme Corp',
        ]);
    }
}
