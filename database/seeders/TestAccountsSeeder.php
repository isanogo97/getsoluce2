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
            ['email' => 'ibrahim.sanogo63@gmail.com'],
            [
                'name' => 'Ibrahim Sanogo',
                'password' => Hash::make('94Valenton@'),
                'role' => 'creator',
                'enterprise_id' => null, // Le créateur n'appartient à aucune entreprise
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('✅ Compte créateur créé/mis à jour : ibrahim.sanogo63@gmail.com');
        
        // 2. Créer une entreprise de test
        $enterprise = Enterprise::firstOrCreate(
            ['site_number' => 'SITE-00001'],
            [
                'name' => 'Entreprise Test GetSoluce',
                'address' => '123 Avenue des Tests, 75001 Paris',
                'contact_first_name' => 'Jean',
                'contact_last_name' => 'Dupont',
                'contact_email' => 'contact@getsoluce.com',
                'contact_phone' => '0123456789',
                'is_active' => true,
                'billing_rate' => 50.00,
            ]
        );
        
        $this->command->info('✅ Entreprise de test créée : ' . $enterprise->name);
        
        // 3. Créer ou mettre à jour le compte ADMIN
        $admin = User::updateOrCreate(
            ['email' => 'admin@getsoluce.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'enterprise_id' => $enterprise->id,
                'position' => 'Responsable RH',
                'department' => 'Ressources Humaines',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        
        $this->command->info('✅ Compte admin créé/mis à jour : admin@getsoluce.com');
        
        // 4. Créer ou mettre à jour le compte EMPLOYEE
        $employee = User::updateOrCreate(
            ['email' => 'employee@getsoluce.com'],
            [
                'name' => 'Employé Test',
                'password' => Hash::make('Employee123!'),
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
        
        $this->command->info('✅ Compte employé créé/mis à jour : employee@getsoluce.com');
        
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
                ['Créateur', 'ibrahim.sanogo63@gmail.com', '94Valenton@'],
                ['Admin', 'admin@getsoluce.com', 'Admin123!'],
                ['Employé', 'employee@getsoluce.com', 'Employee123!'],
            ]
        );
        $this->command->info('');
        $this->command->info('Entreprise de test : ' . $enterprise->name . ' (' . $enterprise->site_number . ')');
    }
}
