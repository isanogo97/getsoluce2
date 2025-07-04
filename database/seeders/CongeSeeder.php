<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conge;
use App\Models\User;

class CongeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Conge::create([
            'user_id'    => $user->id,
            'date_debut' => now()->addDays(7),
            'date_fin'   => now()->addDays(14),
            'type'       => 'CP',
            'motif'      => 'Vacances annuelles',
            'justificatif' => null,
            'statut'     => 'en_attente',
        ]);
    }
}
