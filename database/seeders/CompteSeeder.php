<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compte;
use App\Models\User;

class CompteSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        Compte::create([
            'user_id'        => $user->id,
            'libelle'        => 'Compte courant',
            'montant_initial'=> 1000.00,
            'solde_restant'  => 1000.00,
        ]);
    }
}
