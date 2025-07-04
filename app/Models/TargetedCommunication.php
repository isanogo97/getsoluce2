<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TargetedCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'enterprise_id',
        'user_id',
        'subject',
        'message',
        'priority',
        'target_type',
        'target_ids',
        'expires_at',
        'is_pinned',
    ];

    protected $casts = [
        'target_ids' => 'array',
        'expires_at' => 'datetime',
        'is_pinned' => 'boolean',
    ];

    /**
     * Relations
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'communication_recipients')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeForUser($query, User $user)
    {
        return $query->whereHas('recipients', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
    }

    /**
     * Méthodes
     */
    public function markAsReadBy(User $user): void
    {
        $this->recipients()->updateExistingPivot($user->id, [
            'read_at' => now(),
        ]);
    }

    public function isReadBy(User $user): bool
    {
        $recipient = $this->recipients()->where('user_id', $user->id)->first();
        return $recipient && $recipient->pivot->read_at !== null;
    }

    public function getUnreadCount(): int
    {
        return $this->recipients()->wherePivotNull('read_at')->count();
    }

    /**
     * Envoi aux destinataires
     */
    public function sendToAll(): void
    {
        $users = $this->enterprise->users()
            ->where('role', 'employee')
            ->where('is_active', true)
            ->pluck('id');
        
        $this->recipients()->sync($users);
    }

    public function sendToDepartment(string $department): void
    {
        $users = $this->enterprise->users()
            ->where('department', $department)
            ->where('is_active', true)
            ->pluck('id');
        
        $this->recipients()->sync($users);
    }

    public function sendToUsers(array $userIds): void
    {
        $this->recipients()->sync($userIds);
    }

    /**
     * Priorités
     */
    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }

    public function getPriorityBadgeClass(): string
    {
        return match($this->priority) {
            'high' => 'badge bg-danger',
            'normal' => 'badge bg-primary',
            'low' => 'badge bg-secondary',
            default => 'badge bg-secondary',
        };
    }

    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            'high' => 'Urgent',
            'normal' => 'Normal',
            'low' => 'Faible',
            default => 'Normal',
        };
    }
}
