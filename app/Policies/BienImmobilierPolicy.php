<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BienImmobilier;

class BienImmobilierPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, BienImmobilier $bien): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAgence() && $user->agence->estValidee() && $user->agence->aAbonnementActif();
    }

    public function update(User $user, BienImmobilier $bien): bool
    {
        return $user->id === $bien->agence->user_id;
    }

    public function delete(User $user, BienImmobilier $bien): bool
    {
        return $user->id === $bien->agence->user_id || $user->isAdmin();
    }
}