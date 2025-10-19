<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    protected $fillable = [
        'user_id',
        'libelle',
        'montant_initial',
        'solde_restant',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
