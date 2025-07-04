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
            'user_id'      => $user->id,
            'date_debut'   => now()->addDays(5),
            'date_fin'     => now()->addDays(10),
            'type'         => 'CP',
            'motif'        => 'Vacances',
            'justificatif' => null,
            'statut'       => 'En attente',
        ]);
    }
}
