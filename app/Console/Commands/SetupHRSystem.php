<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Hash;

class SetupHRSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:setup {--fresh : Réinitialiser complètement}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure le système HR avec les comptes de test et les dashboards par rôle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Configuration du système HR Multi-Entreprises');
        $this->info('==============================================');
        
        if ($this->option('fresh')) {
            if (!$this->confirm('⚠️  Cela va réinitialiser les données. Continuer ?')) {
                return;
            }
        }
        
        // 1. Créer l'entreprise de test
        $this->info('🏢 Création de l\'entreprise de test...');
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
        $this->line('✅ Entreprise créée : ' . $enterprise->name);
        
        // 2. Créer/Mettre à jour le compte créateur
        $this->info('👑 Configuration du compte créateur...');
        $creator = User::updateOrCreate(
            ['email' => env('CREATOR_EMAIL')],
            [
                'name' => 'Ibrahim Sanogo',
                'password' => Hash::make(env('CREATOR_PASSWORD')),
                'role' => 'creator',
                'enterprise_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'date_of_birth' => '1990-03-15',
            ]
        );
        $this->line('✅ Créateur : '.env('CREATOR_EMAIL').' / '.env('CREATOR_PASSWORD'));
        
        // 3. Créer/Mettre à jour le compte admin
        $this->info('🏢 Configuration du compte admin...');
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
                'date_of_birth' => '1985-05-15',
                'hourly_rate' => 35.00,
                'gross_salary' => 4500.00,
                'max_advance_percentage' => 30,
            ]
        );
        $this->line('✅ Admin : '.env('ADMIN_EMAIL').' / '.env('ADMIN_PASSWORD'));
        
        // 4. Créer/Mettre à jour le compte employé
        $this->info('👤 Configuration du compte employé...');
        $employee = User::updateOrCreate(
            ['email' => env('EMPLOYEE_EMAIL')],
            [
                'name' => 'Employé Test',
                'password' => Hash::make(env('EMPLOYEE_PASSWORD')),
                'role' => 'employee',
                'enterprise_id' => $enterprise->id,
                'position' => 'Développeur',
                'department' => 'IT',
                'is_active' => true,
                'email_verified_at' => now(),
                'date_of_birth' => now()->subYears(30)->setMonth(now()->month)->setDay(now()->day),
                'hourly_rate' => 25.00,
                'gross_salary' => 3500.00,
                'max_advance_percentage' => 30,
                'invited_by' => $admin->id,
                'invitation_accepted_at' => now(),
            ]
        );
        $this->line('✅ Employé : '.env('EMPLOYEE_EMAIL').' / '.env('EMPLOYEE_PASSWORD'));
        $this->line('   → Anniversaire aujourd\'hui ! 🎂');
        $this->line('   → Avance disponible : ' . number_format($employee->getMaxAdvanceAmount(), 2) . '€');
        
        // 5. Créer quelques données de test
        if ($this->option('fresh') || $employee->absences()->count() === 0) {
            $this->info('📊 Création de données de test...');
            
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
            
            // Avance acceptée
            $employee->avances()->create([
                'montant' => 500,
                'raison' => 'Avance sur salaire',
                'statut' => 'Accepté',
                'created_at' => now()->startOfMonth(),
            ]);
            
            // Communication
            \App\Models\Communication::create([
                'user_id' => $admin->id,
                'enterprise_id' => $enterprise->id,
                'message' => 'Bienvenue dans le nouveau système HR ! N\'hésitez pas à explorer les différentes fonctionnalités.',
            ]);
            
            $this->line('✅ Données de test créées');
        }
        
        // 6. Afficher le récapitulatif
        $this->info('');
        $this->info('🎉 CONFIGURATION TERMINÉE !');
        $this->info('==========================');
        
        $this->table(
            ['Rôle', 'Email', 'Mot de passe', 'Dashboard'],
            [
                ['👑 Créateur', env('CREATOR_EMAIL'), env('CREATOR_PASSWORD'), 'Vue globale toutes entreprises'],
                ['🏢 Admin', env('ADMIN_EMAIL'), env('ADMIN_PASSWORD'), 'Gestion entreprise + validations'],
                ['👤 Employé', env('EMPLOYEE_EMAIL'), env('EMPLOYEE_PASSWORD'), 'Dashboard personnel + avances'],
            ]
        );
        
        $this->info('');
        $this->info('🌐 Démarrez le serveur : php artisan serve');
        $this->info('🔗 Puis allez sur : http://localhost:8000/login');
        $this->info('');
        
        // Vérifier les middlewares
        if (!class_exists(\App\Http\Middleware\CheckRole::class)) {
            $this->warn('⚠️  Le middleware CheckRole n\'existe pas !');
            $this->warn('   Créez-le avec : php artisan make:middleware CheckRole');
        }
        
        if (!class_exists(\App\Http\Middleware\CheckEnterprise::class)) {
            $this->warn('⚠️  Le middleware CheckEnterprise n\'existe pas !');
            $this->warn('   Créez-le avec : php artisan make:middleware CheckEnterprise');
        }
        
        return Command::SUCCESS;
    }
}