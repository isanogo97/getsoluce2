<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Conge;

class CongePolicy
{
    /**
     * L'utilisateur peut voir tous les congés.
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * L'utilisateur peut voir un congé s’il est admin ou propriétaire.
     */
    public function view(User $user, Conge $conge)
    {
        return $user->is_admin || $user->id === $conge->user_id;
    }

    /**
     * Tous les utilisateurs peuvent créer un congé.
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Seul l’utilisateur propriétaire peut modifier un congé s’il est en attente.
     */
    public function update(User $user, Conge $conge)
    {
        return $user->id === $conge->user_id && $conge->statut === 'en_attente';
    }

    /**
     * Seul l’utilisateur propriétaire peut supprimer un congé s’il est en attente.
     */
    public function delete(User $user, Conge $conge)
    {
        return $user->id === $conge->user_id && $conge->statut === 'en_attente';
    }

    /**
     * Seuls les administrateurs peuvent valider un congé.
     */
    public function valider(User $user, Conge $conge)
    {
        return $user->is_admin;
    }
}
