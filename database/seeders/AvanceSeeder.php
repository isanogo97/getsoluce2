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
            'user_id'       => $user->id,
            'montant'       => 500,
            'date_echeance' => now()->addMonth(),
            'motif'         => 'Avance sur salaire',
            'statut'        => 'En attente',
        ]);
    }
}
