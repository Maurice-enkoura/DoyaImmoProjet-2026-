<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Activation des abonnements
    |--------------------------------------------------------------------------
    |
    | Mettez à false pour désactiver le système d'abonnement temporairement.
    | Les agences pourront utiliser toutes les fonctionnalités gratuitement.
    |
    */
    'actif' => env('ABONNEMENT_ACTIF', false),

    /*
    |--------------------------------------------------------------------------
    | Mode gratuit
    |--------------------------------------------------------------------------
    |
    | Si activé, toutes les agences ont les fonctionnalités Pro gratuitement.
    |
    */
    'mode_gratuit' => env('ABONNEMENT_MODE_GRATUIT', true),

    /*
    |--------------------------------------------------------------------------
    | Plans disponibles
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'basic' => [
            'label' => 'Gratuit',
            'prix' => 0,
            'limite_offres' => 999,
            'peut_publier_biens' => true,
        ],
        'pro' => [
            'label' => 'Pro',
            'prix' => 5000,
            'limite_offres' => PHP_INT_MAX,
            'peut_publier_biens' => true,
        ],
    ],
];