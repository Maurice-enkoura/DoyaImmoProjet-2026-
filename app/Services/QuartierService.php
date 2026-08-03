<?php

namespace App\Services;

use App\Models\Quartier;
use Illuminate\Support\Collection;

class QuartierService
{
    /**
     * Récupère tous les quartiers actifs
     */
    public function getQuartiersActifs(): Collection
    {
        return Quartier::actif()->orderBy('nom')->get();
    }

    /**
     * Récupère les quartiers groupés par ville
     */
    public function getQuartiersGroupes(): array
    {
        return Quartier::actif()
            ->orderBy('ville')
            ->orderBy('nom')
            ->get()
            ->groupBy('ville')
            ->toArray();
    }

    /**
     * Récupère un quartier par son nom
     */
    public function getQuartierByNom(string $nom): ?Quartier
    {
        return Quartier::where('nom', $nom)->first();
    }

    /**
     * Vérifie si un quartier existe
     */
    public function quartierExiste(string $nom): bool
    {
        return Quartier::where('nom', $nom)->exists();
    }

    /**
     * Obtient les suggestions de quartiers pour l'autocomplétion
     */
    public function getSuggestions(string $recherche, int $limit = 10): Collection
    {
        return Quartier::actif()
            ->where('nom', 'like', "%{$recherche}%")
            ->orderBy('nom')
            ->limit($limit)
            ->get(['id', 'nom', 'ville']);
    }

    /**
     * Valide qu'un quartier est valide
     */
    public function validerQuartier(string $nom): bool
    {
        return $this->quartierExiste($nom);
    }

    /**
     * Récupère les statistiques d'un quartier
     */
    public function getStatistiques(Quartier $quartier): array
    {
        return [
            'total_demandes' => $quartier->demandes()->count(),
            'demandes_en_attente' => $quartier->demandes()->where('statut', 'en_attente')->count(),
            'total_biens' => $quartier->biens()->count(),
            'biens_disponibles' => $quartier->biens()->where('statut', true)->count(),
            'total_agences' => $quartier->agences()->count(),
            'agences_validees' => $quartier->agences()->where('statut_validation', true)->count(),
            'note_moyenne' => $quartier->agences()->withAvg('evaluations', 'note')
                ->whereHas('evaluations')
                ->avg('evaluations_avg_note') ?? 0,
        ];
    }

    /**
     * Récupère les quartiers les plus populaires
     */
    public function getQuartiersPopulaires(int $limit = 10): Collection
    {
        return Quartier::withCount(['demandes', 'biens'])
            ->actif()
            ->having('demandes_count', '>', 0)
            ->orHaving('biens_count', '>', 0)
            ->orderBy('demandes_count', 'desc')
            ->limit($limit)
            ->get();
    }
}