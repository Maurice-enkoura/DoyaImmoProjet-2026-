<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Signalement;

class SignalementPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Signalement $signalement): bool
    {
        return $user->isAdmin() || $user->id === $signalement->particulier->user_id;
    }

    public function create(User $user): bool
    {
        return $user->isParticulier();
    }

    public function update(User $user, Signalement $signalement): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Signalement $signalement): bool
    {
        return $user->isAdmin();
    }
}