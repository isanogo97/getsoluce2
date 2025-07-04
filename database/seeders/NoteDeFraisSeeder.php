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
            'date'         => now()->subDay(),
            'montant'      => 42.50,
            'description'  => 'Repas client',
            'justificatif' => 'exemple.pdf',
            'statut'       => 'En attente',
        ]);
    }
}
