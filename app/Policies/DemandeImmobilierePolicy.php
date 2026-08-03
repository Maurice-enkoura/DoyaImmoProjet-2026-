<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DemandeImmobiliere;

class DemandeImmobilierePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DemandeImmobiliere $demande): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isParticulier();
    }

    public function update(User $user, DemandeImmobiliere $demande): bool
    {
        return $user->id === $demande->particulier->user_id;
    }

    public function delete(User $user, DemandeImmobiliere $demande): bool
    {
        return $user->id === $demande->particulier->user_id;
    }
}