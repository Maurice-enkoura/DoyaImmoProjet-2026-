<?php

namespace App\Services;

use App\Models\User;

class RedirectService
{
    public function getRedirectUrl(User $user): string
    {
        if ($user->isAdmin()) {
            return route('admin.dashboard');
        }

        if ($user->isAgence()) {
            $agence = $user->agence;
            if (!$agence || !$agence->statut_validation) {
                return route('agence.dashboard');
            }
            return route('agence.dashboard');
        }

        return route('particulier.dashboard');
    }

    public function getRedirectMessage(User $user): ?array
    {
        if ($user->isAgence()) {
            $agence = $user->agence;
            if (!$agence || !$agence->statut_validation) {
                return [
                    'type' => 'info',
                    'message' => 'Votre agence est en attente de validation par un administrateur.'
                ];
            }
        }

        return null;
    }
}