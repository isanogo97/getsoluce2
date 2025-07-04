<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Absence;

class AbsencePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Absence $absence): bool
    {
        return $user->is_admin || $absence->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Absence $absence): bool
    {
        if ($user->is_admin) {
            return true;
        }

        return $absence->user_id === $user->id
            && $absence->statut === 'en_attente';
    }

    public function delete(User $user, Absence $absence): bool
    {
        return $absence->user_id === $user->id
            && $absence->statut === 'en_attente';
    }

    public function validate(User $user, Absence $absence): bool
    {
        return $user->is_admin;
    }

    // Autorise le refus (seuls les admins)
    public function reject(User $user, Absence $absence): bool
    {
        return $user->is_admin;
    }
}
