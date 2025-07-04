<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\{Absence, Conge, NoteDeFrais, Compte, Avance};
use App\Policies\{AbsencePolicy, CongePolicy, NoteDeFraisPolicy, ComptePolicy, AvancePolicy};

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Absence::class     => AbsencePolicy::class,
        Conge::class       => CongePolicy::class,
        NoteDeFrais::class => NoteDeFraisPolicy::class,
        Compte::class      => ComptePolicy::class,
        Avance::class      => AvancePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // gate simple "admin"
        Gate::define('admin', fn($user) => (bool) $user->is_admin);
    }
}
