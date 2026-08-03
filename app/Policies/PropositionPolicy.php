<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Proposition;

class PropositionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Proposition $proposition): bool
    {
        return $user->id === $proposition->agence->user_id || 
               $user->id === $proposition->particulier->user_id ||
               $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAgence() && $user->agence->estValidee() && $user->agence->aAbonnementActif();
    }

    public function update(User $user, Proposition $proposition): bool
    {
        return $user->id === $proposition->agence->user_id || 
               $user->id === $proposition->particulier->user_id;
    }

    public function delete(User $user, Proposition $proposition): bool
    {
        return $user->isAdmin();
    }
}