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
        if (!$user) {
            return;
        }

        $jours = 10;
        $brut  = $jours * 7 * 11.65;
        $net   = round($brut * 0.77, 2);

        Avance::create([
            'user_id'          => $user->id,
            'jours_travailles' => $jours,
            'montant_brut'     => $brut,
            'montant_net'      => $net,
            'statut'           => 'en_attente',
        ]);
    }
}
