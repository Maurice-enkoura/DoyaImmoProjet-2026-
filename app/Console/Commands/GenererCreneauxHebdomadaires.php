<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Agence;
use App\Models\CreneauRendezVous;
use Carbon\Carbon;

class GenererCreneauxHebdomadaires extends Command
{
    protected $signature = 'creneaux:generer-hebdomadaire';
    protected $description = 'Génère automatiquement les créneaux pour toutes les agences pour la semaine à venir';

    public function handle()
    {
        $this->info('🔄 Génération automatique des créneaux...');

        // Récupérer toutes les agences
        $agences = Agence::all();
        $totalCreneaux = 0;
        $totalAgences = 0;

        foreach ($agences as $agence) {
            // Récupérer le planning type de l'agence (stocké en session ou en base)
            // ⚠️ Ici on suppose que le planning est stocké en session
            // Pour une solution durable, il faudrait le stocker en base de données
            $planningType = session()->get('planning_type_' . $agence->id, []);

            if (empty($planningType)) {
                continue;
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
     * Générer les créneaux pour une agence spécifique
     */
    private function genererCreneauxPourAgence($agence, $planningType): int
    {
        if (empty($planningType)) {
            return 0;
        }

        $creneauxCrees = 0;
        $joursMap = [
            0 => 'Lun',
            1 => 'Mar',
            2 => 'Mer',
            3 => 'Jeu',
            4 => 'Ven',
            5 => 'Sam',
            6 => 'Dim'
        ];

        // Générer pour les 7 prochains jours (à partir de demain)
        for ($i = 1; $i <= 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $jourSemaine = $date->dayOfWeek;
            $jourSemaineCarbon = $jourSemaine === 0 ? 6 : $jourSemaine - 1;

            // Récupérer les plannings pour ce jour
            $planningsJour = array_filter($planningType, function ($p) use ($jourSemaineCarbon) {
                return $p['jour'] == $jourSemaineCarbon;
            });

            foreach ($planningsJour as $planning) {
                // Vérifier si le créneau existe déjà
                $existe = CreneauRendezVous::where('agence_id', $agence->id)
                    ->where('date', $date->format('Y-m-d'))
                    ->where('heure_debut', $planning['heure_debut'])
                    ->exists();

                if (!$existe) {
                    CreneauRendezVous::create([
                        'agence_id' => $agence->id,
                        'date' => $date->format('Y-m-d'),
                        'heure_debut' => $planning['heure_debut'],
                        'heure_fin' => $planning['heure_fin'],
                        'est_disponible' => true,
                    ]);
                    $creneauxCrees++;
                }
            }
        }

        return $creneauxCrees;
    }
}