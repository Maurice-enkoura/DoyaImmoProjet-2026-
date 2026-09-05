<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanningType extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'jour',
        'heure_debut',
        'heure_fin',
    ];

    protected $casts = [
        'jour' => 'integer',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    /**
     * Récupère les jours de la semaine en français
     */
    public static function getJoursFr(): array
    {
        return ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    }

    /**
     * Récupère le planning type d'une agence formaté pour l'affichage
     */
    public static function getPlanningFormatted($agenceId): array
    {
        $plannings = self::where('agence_id', $agenceId)->get();
        $joursFr = self::getJoursFr();
        $result = [];

        foreach ($plannings as $planning) {
            $jour = $planning->jour;
            if (!isset($result[$jour])) {
                $result[$jour] = [
                    'jour' => $jour,
                    'jour_fr' => $joursFr[$jour] ?? '?',
                    'creneaux' => []
                ];
            }
            $result[$jour]['creneaux'][] = [
                'heure_debut' => $planning->heure_debut,
                'heure_fin' => $planning->heure_fin
            ];
        }

        return array_values($result);
    }
}