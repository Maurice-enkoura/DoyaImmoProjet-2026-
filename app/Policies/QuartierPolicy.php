<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Quartier;

class QuartierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Quartier $quartier): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Quartier $quartier): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Quartier $quartier): bool
    {
        return $user->isAdmin();
    }

    public function toggle(User $user, Quartier $quartier): bool
    {
        return $user->isAdmin();
    }

    public function import(User $user): bool
    {
        return $user->isAdmin();
    }

    public function export(User $user): bool
    {
        return $user->isAdmin();
    }
}