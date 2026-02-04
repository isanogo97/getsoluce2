<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_SALARIE = 'ROLE_SALARIE';
    public const ROLE_MANAGER = 'ROLE_MANAGER';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'role',
        'poste',
        'salaire_brut',
        'taux_horaire',
        'jours_ouvres',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    // Relations
    public function absences()
    {
        return $this->hasMany(Absence::class);
    }

    public function avances()
    {
        return $this->hasMany(Avance::class);
    }

    public function notesDeFrais()
    {
        return $this->hasMany(NoteDeFrais::class);
    }

    public function comptes()
    {
        return $this->hasMany(Compte::class);
    }

    public function conges()
    {
        return $this->hasMany(Conge::class);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
public function enterprise()
{
    return $this->belongsTo(Enterprise::class);
}

}
