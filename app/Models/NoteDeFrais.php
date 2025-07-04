<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoteDeFrais extends Model
{
    use HasFactory;

    // Spécifie le nom exact de la table
    protected $table = 'notes_de_frais';

    protected $fillable = [
        'user_id',
        'date',
        'montant',
        'description',
        'justificatif',  // AJOUTÉ - champ manquant
        'statut',
    ];

    protected $casts = [
        'date' => 'date',
        'montant' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}