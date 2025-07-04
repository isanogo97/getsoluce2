<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer ou mettre à jour le compte CRÉATEUR
        $creator = User::updateOrCreate(
            ['email' => env('CREATOR_EMAIL')],
            [
                'name' => 'Ibrahim Sanogo',
                'password' => Hash::make(env('CREATOR_PASSWORD')),
                'role' => 'creator',
                'enterprise_id' => null, // Le créateur n'appartient à aucune entreprise
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Compte créateur créé/mis à jour : '.env('CREATOR_EMAIL'));
        
        // 2. Créer une entreprise de test
        $enterprise = Enterprise::firstOrCreate(
            ['site_number' => 'SITE-00001'],
            [
                'name' => 'Entreprise Test GetSoluce',
                'address' => '123 Avenue des Tests, 75001 Paris',
                'contact_first_name' => 'Jean',
                'contact_last_name' => 'Dupont',
                'contact_email' => env('CONTACT_EMAIL'),
                'contact_phone' => '0123456789',
                'is_active' => true,
                'billing_rate' => 50.00,
            ]
        );
        
        $this->command->info('✅ Entreprise de test créée : ' . $enterprise->name);
        
        // 3. Créer ou mettre à jour le compte ADMIN
        $admin = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name' => 'Admin Test',
                'password' => Hash::make(env('ADMIN_PASSWORD')),
                'role' => 'admin',
                'enterprise_id' => $enterprise->id,
                'position' => 'Responsable RH',
                'department' => 'Ressources Humaines',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('✅ Compte admin créé/mis à jour : '.env('ADMIN_EMAIL'));
        
        // 4. Créer ou mettre à jour le compte EMPLOYEE
        $employee = User::updateOrCreate(
            ['email' => env('EMPLOYEE_EMAIL')],
            [
                'name' => 'Employé Test',
                'password' => Hash::make(env('EMPLOYEE_PASSWORD')),
                'role' => 'employee',
                'enterprise_id' => $enterprise->id,
                'position' => 'Développeur',
                'department' => 'IT',
                'hourly_rate' => 25.00,
                'gross_salary' => 3500.00,
                'max_advance_percentage' => 30,
                'is_active' => true,
                'email_verified_at' => now(),
                'invited_by' => $admin->id,
                'invitation_accepted_at' => now(),
            ]
        );
        
        $this->command->info('✅ Compte employé créé/mis à jour : '.env('EMPLOYEE_EMAIL'));
        
        // 5. Créer quelques données de test pour l'employé
        if ($employee->absences()->count() === 0) {
            // Absence validée
            $employee->absences()->create([
                'date' => now()->subDays(10),
                'raison' => 'Rendez-vous médical',
                'justification' => 'Certificat médical',
                'statut' => 'Accepté',
            ]);
            
            // Absence en attente
            $employee->absences()->create([
                'date' => now()->addDays(5),
                'raison' => 'Formation professionnelle',
                'justification' => 'Convocation formation',
                'statut' => 'En attente',
            ]);
            
            $this->command->info('✅ Données de test créées pour l\'employé');
        }
        
        // 6. Afficher le récapitulatif
        $this->command->info('');
        $this->command->info('🎉 COMPTES DE TEST CRÉÉS AVEC SUCCÈS !');
        $this->command->info('=====================================');
        $this->command->table(
            ['Rôle', 'Email', 'Mot de passe'],
            [
                ['Créateur', env('CREATOR_EMAIL'), env('CREATOR_PASSWORD')],
                ['Admin', env('ADMIN_EMAIL'), env('ADMIN_PASSWORD')],
                ['Employé', env('EMPLOYEE_EMAIL'), env('EMPLOYEE_PASSWORD')],
            ]
        );
        $this->command->info('');
        $this->command->info('Entreprise de test : ' . $enterprise->name . ' (' . $enterprise->site_number . ')');
    }
}