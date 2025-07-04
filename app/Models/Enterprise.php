<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'site_number',
        'address',
        'contact_first_name',
        'contact_last_name',
        'contact_email',
        'contact_phone',
        'is_active',
        'billing_rate',
        'last_activity_at',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_activity_at' => 'datetime',
        'settings' => 'array',
        'billing_rate' => 'decimal:2',
    ];

    /**
     * Relations
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function employees()
    {
        return $this->hasMany(User::class)->where('role', 'employee');
    }

    public function admins()
    {
        return $this->hasMany(User::class)->where('role', 'admin');
    }

    public function absences()
    {
        return $this->hasManyThrough(\App\Models\Absence::class, User::class);
    }

    public function conges()
    {
        return $this->hasManyThrough(\App\Models\Conge::class, User::class);
    }

    public function avances()
    {
        return $this->hasManyThrough(\App\Models\Avance::class, User::class);
    }

    public function notesDeFrais()
    {
        return $this->hasManyThrough(\App\Models\NoteDeFrais::class, User::class);
    }

    public function invitations()
    {
        return $this->hasMany(\App\Models\Invitation::class);
    }

    /**
     * Méthodes utilitaires
     */
    public function getMonthlyRevenue()
    {
        $employeeCount = $this->employees()->count();
        $billingRate = $this->billing_rate ?? 50;
        return $employeeCount * $billingRate;
    }
}
