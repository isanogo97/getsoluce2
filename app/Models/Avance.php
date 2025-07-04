<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Avance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jours_travailles',
        'montant_brut',
        'montant_net',
        'statut'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
