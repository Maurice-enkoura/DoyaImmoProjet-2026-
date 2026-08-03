<?php

namespace App\Services;

use App\Models\DemandeImmobiliere;
use App\Models\BienImmobilier;
use App\Models\Agence;
use Illuminate\Support\Collection;

class MatchingService
{
    /**
     * Calcule le score de compatibilité entre une demande et un bien
     */
    public function calculerScore(DemandeImmobiliere $demande, BienImmobilier $bien): array
    {
        $score = 0;
        $details = [];
        $criteres = [];

        // === CRITÈRES OBLIGATOIRES ===

        // 1. Type d'opération (OBLIGATOIRE - 25 points)
        if ($demande->type_operation === $bien->type_contrat) {
            $score += 25;
            $criteres['type_operation'] = true;
            $details['type_operation'] = '✅ Type d\'opération: ' . $demande->type_operation->label();
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['Type d\'opération ne correspond pas']];
        }

        // 2. Type de bien (OBLIGATOIRE - 25 points)
        if ($demande->type_bien === $bien->type_bien) {
            $score += 25;
            $criteres['type_bien'] = true;
            $details['type_bien'] = '✅ Type de bien: ' . $demande->type_bien->label();
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['Type de bien ne correspond pas']];
        }

        // 3. Zone géographique (OBLIGATOIRE - 20 points)
        if (strtolower($demande->zone_recherchee) === strtolower($bien->quartier)) {
            $score += 20;
            $criteres['zone'] = true;
            $details['zone'] = '✅ Zone: ' . $demande->zone_recherchee;
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['Zone géographique ne correspond pas']];
        }

        // === CRITÈRES DE COMPATIBILITÉ ===

        // 4. Budget (20 points avec tolérance ±20%)
        $tolerance = 0.20;
        $prixBien = $bien->prix;
        $budgetMax = $demande->budget_maximum;
        $seuilMax = $budgetMax * (1 + $tolerance);

        if ($prixBien <= $budgetMax) {
            $score += 20;
            $criteres['budget'] = true;
            $details['budget'] = '✅ Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F ≤ ' . number_format($budgetMax, 0, ',', ' ') . ' F';
        } elseif ($prixBien <= $seuilMax) {
            $score += 12;
            $criteres['budget'] = 'partiel';
            $details['budget'] = '⚠️ Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F (tolérance +20%)';
        } else {
            $score += 0;
            $criteres['budget'] = false;
            $details['budget'] = '❌ Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F > ' . number_format($budgetMax, 0, ',', ' ') . ' F';
        }

        // 5. Surface (10 points avec tolérance ±20%)
        if ($demande->surface_minimum) {
            $surfaceMin = $demande->surface_minimum;
            $surfaceMinTolere = $surfaceMin * 0.8;

            if ($surfaceMin <= $bien->surface) {
                $score += 10;
                $criteres['surface'] = true;
                $details['surface'] = '✅ Surface: ' . $surfaceMin . ' m² ≤ ' . $bien->surface . ' m²';
            } elseif ($surfaceMinTolere <= $bien->surface) {
                $score += 6;
                $criteres['surface'] = 'partiel';
                $details['surface'] = '⚠️ Surface: ' . $surfaceMin . ' m² (tolérance -20%)';
            } else {
                $score += 0;
                $criteres['surface'] = false;
                $details['surface'] = '❌ Surface: ' . $surfaceMin . ' m² > ' . $bien->surface . ' m²';
            }
        }

        // 6. Nombre de chambres (10 points avec tolérance -1)
        if ($demande->nombre_chambres) {
            $chambresDemande = $demande->nombre_chambres;
            $chambresBien = $bien->nombre_chambres;

            if ($chambresDemande <= $chambresBien) {
                $score += 10;
                $criteres['chambres'] = true;
                $details['chambres'] = '✅ Chambres: ' . $chambresDemande . ' ≤ ' . $chambresBien;
            } elseif ($chambresDemande - 1 <= $chambresBien) {
                $score += 6;
                $criteres['chambres'] = 'partiel';
                $details['chambres'] = '⚠️ Chambres: ' . $chambresDemande . ' (tolérance -1)';
            } else {
                $score += 0;
                $criteres['chambres'] = false;
                $details['chambres'] = '❌ Chambres: ' . $chambresDemande . ' > ' . $chambresBien;
            }
        }

        // Score maximum 100
        $scoreTotal = min(100, $score);

        // Niveau de correspondance
        $niveau = $this->getNiveau($scoreTotal);

        return [
            'score' => $scoreTotal,
            'niveau' => $niveau,
            'criteres' => $criteres,
            'details' => $details
        ];
    }

    /**
     * Récupère les demandes compatibles pour une agence
     */
    public function getDemandesCompatibles(Agence $agence, int $perPage = 12)
    {
        $biens = $agence->biens()->where('statut', true)->get();

        if ($biens->isEmpty()) {
            return collect();
        }

        $demandes = DemandeImmobiliere::where('statut', 'en_attente')->get();
        $resultats = [];

        foreach ($demandes as $demande) {
            $meilleurScore = 0;
            $meilleurBien = null;
            $meilleursDetails = [];

            foreach ($biens as $bien) {
                $match = $this->calculerScore($demande, $bien);
                if ($match['score'] > $meilleurScore) {
                    $meilleurScore = $match['score'];
                    $meilleurBien = $bien;
                    $meilleursDetails = $match['details'];
                }
            }

            if ($meilleurScore > 0) {
                $resultats[] = (object) [
                    'demande' => $demande,
                    'score' => $meilleurScore,
                    'niveau' => $this->getNiveau($meilleurScore),
                    'bien' => $meilleurBien,
                    'details' => $meilleursDetails
                ];
            }
        }

        // Trier par score décroissant
        usort($resultats, function ($a, $b) {
            return $b->score - $a->score;
        });

        return collect($resultats);
    }

    /**
     * Récupère toutes les demandes avec leur score de compatibilité
     */
    public function getDemandesAvecScore(Agence $agence, int $perPage = 12)
    {
        $biens = $agence->biens()->where('statut', true)->get();

        $demandes = DemandeImmobiliere::where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        if ($biens->isEmpty()) {
            foreach ($demandes as $demande) {
                $demande->score = 0;
                $demande->niveau = 'Aucun bien';
                $demande->bien = null;
            }
            return $demandes;
        }

        foreach ($demandes as $demande) {
            $meilleurScore = 0;
            $meilleurBien = null;

            foreach ($biens as $bien) {
                $match = $this->calculerScore($demande, $bien);
                if ($match['score'] > $meilleurScore) {
                    $meilleurScore = $match['score'];
                    $meilleurBien = $bien;
                }
            }

            $demande->score = $meilleurScore;
            $demande->niveau = $meilleurScore > 0 ? $this->getNiveau($meilleurScore) : 'Aucune correspondance';
            $demande->bien = $meilleurBien;
        }

        return $demandes;
    }

    /**
     * Récupère le nombre de demandes compatibles
     */
    public function countDemandesCompatibles(Agence $agence): int
    {
        $biens = $agence->biens()->where('statut', true)->get();

        if ($biens->isEmpty()) {
            return 0;
        }

        $demandes = DemandeImmobiliere::where('statut', 'en_attente')->get();
        $count = 0;

        foreach ($demandes as $demande) {
            foreach ($biens as $bien) {
                $match = $this->calculerScore($demande, $bien);
                if ($match['score'] > 0) {
                    $count++;
                    break;
                }
            }
        }

        return $count;
    }

    private function getNiveau(int $score): string
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Bon';
        if ($score >= 40) return 'Moyen';
        if ($score >= 20) return 'Faible';
        return 'Minimal';
    }
}