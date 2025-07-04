<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Avance;
use App\Models\User;

class AvanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Avance::create([
            'user_id'          => $user->id,
            'jours_travailles' => 10,
            'montant_brut'     => 10 * 7 * 11.65,
            'montant_net'      => round(10 * 7 * 11.65 * 0.77, 2),
            'statut'           => 'validée',
        ]);
    }
}
