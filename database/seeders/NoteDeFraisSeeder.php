<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NoteDeFrais;
use App\Models\User;

class NoteDeFraisSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        NoteDeFrais::create([
            'user_id'      => $user->id,
            'date'         => now()->subDays(3),
            'montant'      => 75.50,
            'description'  => 'Déjeuner client',
            'justificatif' => 'justificatifs/dejeuner.pdf',
            'statut'       => 'en_attente',
        ]);
    }
}
