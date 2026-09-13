<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agence;
use App\Models\PlanningType;
use App\Models\CreneauRendezVous;
use Carbon\Carbon;

class GenererCreneauxHebdomadaires extends Command
{
    protected $signature = 'creneaux:generer-hebdomadaire';
    protected $description = 'Génère automatiquement les créneaux pour toutes les agences pour les 30 prochains jours';

    public function handle()
    {
        $this->info('🔄 Génération automatique des créneaux (fenêtre 30 jours)...');

        $agences = Agence::all();
        $totalCreneaux = 0;
        $totalAgences = 0;

        foreach ($agences as $agence) {
            $planningsBdd = PlanningType::where('agence_id', $agence->id)->get();
            if ($planningsBdd->isEmpty()) {
                continue;
            }

            $planningType = [];
            foreach ($planningsBdd as $p) {
                $planningType[] = [
                    'jour' => (int) $p->jour,
                    'heure_debut' => substr($p->heure_debut, 0, 5),
                    'heure_fin' => substr($p->heure_fin, 0, 5),
                ];
            }

            $creneauxCrees = $this->genererCreneauxPourAgence($agence, $planningType);
            
            if ($creneauxCrees > 0) {
                $totalCreneaux += $creneauxCrees;
                $totalAgences++;
                $this->line("   ✅ Agence #{$agence->id} ({$agence->nom_agence}) : {$creneauxCrees} créneaux générés");
            }
        }

        $this->info("✅ {$totalCreneaux} créneaux générés pour {$totalAgences} agence(s).");
        
        return Command::SUCCESS;
    }

    /**
     * Générer les créneaux pour une agence spécifique sur 30 jours
     */
    private function genererCreneauxPourAgence($agence, array $planningType): int
    {
        if (empty($planningType)) {
            return 0;
        }

        $creneauxCrees = 0;

        // Générer pour les 30 prochains jours
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today()->addDays($i);
            $jourSemaine = $date->dayOfWeek;
            $jourSemaineCarbon = $jourSemaine === 0 ? 6 : $jourSemaine - 1;

            $planningsJour = array_filter($planningType, function ($p) use ($jourSemaineCarbon) {
                return (int) $p['jour'] === (int) $jourSemaineCarbon;
            });

            foreach ($planningsJour as $planning) {
                $heureDebut = strlen($planning['heure_debut']) === 5 ? $planning['heure_debut'] : substr($planning['heure_debut'], 0, 5);
                $heureFin = strlen($planning['heure_fin']) === 5 ? $planning['heure_fin'] : substr($planning['heure_fin'], 0, 5);

                $existe = CreneauRendezVous::where('agence_id', $agence->id)
                    ->where('date', $date->format('Y-m-d'))
                    ->where('heure_debut', $heureDebut)
                    ->exists();

                if (!$existe) {
                    CreneauRendezVous::create([
                        'agence_id' => $agence->id,
                        'date' => $date->format('Y-m-d'),
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                        'est_disponible' => true,
                    ]);
                    $creneauxCrees++;
                }
            }
        }

        return $creneauxCrees;
    }
}