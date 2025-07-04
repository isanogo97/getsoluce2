<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;  // <— n’oubliez pas d’importer User

class Absence extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'type',
        'motif',
        'statut',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * L’utilisateur qui a déclaré l’absence.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
