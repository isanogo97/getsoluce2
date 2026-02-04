<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'intervention_id',
        'file_path',
    ];

    public function intervention()
    {
        return $this->belongsTo(Intervention::class);
    }
}
