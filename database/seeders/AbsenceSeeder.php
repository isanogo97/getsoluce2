<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Absence;
use App\Models\User;

class AbsenceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // ou ->where('email','admin@acme.local')->first()

        Absence::create([
            'user_id'      => $user->id,
            'date'         => now()->subDay(),
            'motif'        => 'Rendez-vous médical',
            'justificatif' => null,
            'statut'       => 'en attente',
        ]);
    }
}
