<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_number',
        'name',
        'address',
        'comment',
    ];

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
