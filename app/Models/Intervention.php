<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    use HasFactory;

    public const STATUS_EN_ROUTE = 'en_route';
    public const STATUS_SUR_PLACE = 'sur_place';
    public const STATUS_TERMINE = 'termine';
    public const STATUS_EN_ATTENTE = 'en_attente';

    protected $fillable = [
        'site_id',
        'user_id',
        'status',
        'problem_resolved',
        'unresolved_reason',
        'comment',
    ];

    protected $casts = [
        'problem_resolved' => 'boolean',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
