<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Http\Requests\RendezVousRequest;
use App\Models\RendezVous;
use App\Models\Proposition;
use App\Models\CreneauRendezVous;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Notifications\RendezVousConfirmeNotification;
use App\Notifications\RendezVousDemandeNotification;

class RendezVousController extends Controller
{
    public function index()
    {
        $rendezVous = RendezVous::with(['proposition.demande', 'proposition.bien', 'agence.user'])
            ->where('particulier_id', Auth::user()->particulier->id)
            ->orderBy('date_visite', 'asc')
            ->paginate(10);

        return view('particulier.rendezvous.index', compact('rendezVous'));
    }

    public function show(RendezVous $rendezVous)
    {
        if ($rendezVous->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $rendezVous->load(['proposition.demande', 'proposition.bien', 'agence.user']);
        return view('particulier.rendezvous.show', compact('rendezVous'));
    }

    public function create(Proposition $proposition)
{
    if ($proposition->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    if ($proposition->statut !== StatutPropositionEnum::ACCEPTEE) {
        return back()->with('error', 'Cette proposition n\'est pas acceptée.');
    }

    // ✅ S'assurer que les créneaux des 30 prochains jours sont générés depuis le PlanningType en BDD
    if ($proposition->agence) {
        $this->assurerCreneauxAgence($proposition->agence);
    }

    // ✅ Récupérer les créneaux disponibles pour cette agence (avec filtre anti-passé)
    $creneaux = CreneauRendezVous::where('agence_id', $proposition->agence_id)
        ->where('est_disponible', true)
        ->where(function($query) {
            // ✅ Garder les créneaux futurs (date > aujourd'hui)
            $query->where('date', '>', now()->toDateString())
                  // ✅ OU même jour mais heure > maintenant
                  ->orWhere(function($q) {
                      $q->where('date', '=', now()->toDateString())
                        ->where('heure_debut', '>', now()->format('H:i:s'));
                  });
        })
        ->orderBy('date', 'asc')
        ->orderBy('heure_debut', 'asc')
        ->get();

    return view('particulier.rendezvous.create', compact('proposition', 'creneaux'));
}

    /**
     * Génère automatiquement les créneaux manquants des 30 prochains jours pour l'agence
     */
    private function assurerCreneauxAgence($agence): void
    {
        $planningsBdd = \App\Models\PlanningType::where('agence_id', $agence->id)->get();
        if ($planningsBdd->isEmpty()) {
            return;
        }

        $planningType = [];
        foreach ($planningsBdd as $p) {
            $planningType[] = [
                'jour' => (int) $p->jour,
                'heure_debut' => substr($p->heure_debut, 0, 5),
                'heure_fin' => substr($p->heure_fin, 0, 5),
            ];
        }

        for ($i = 0; $i < 30; $i++) {
            $date = \Carbon\Carbon::today()->addDays($i);
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
                }
            }
        }
    }

    public function store(RendezVousRequest $request)
{
    $proposition = Proposition::findOrFail($request->proposition_id);

    if ($proposition->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    // Récupérer le créneau
    $creneau = CreneauRendezVous::findOrFail($request->creneau_id);

    // ✅ Vérifier que le créneau est disponible ET PAS PASSÉ
    $creneauDateTime = \Carbon\Carbon::parse($creneau->date)->setTimeFromTimeString($creneau->heure_debut);
    
    if (!$creneau->est_disponible || $creneauDateTime->isPast()) {
        return back()->with('error', 'Ce créneau n\'est plus disponible.');
    }

    // ✅ Vérifier que l'agence correspond bien à la proposition
    if ($creneau->agence_id !== $proposition->agence_id) {
        return back()->with('error', 'Créneau invalide pour cette proposition.');
    }

    // Marquer le créneau comme indisponible
    $creneau->update(['est_disponible' => false]);

    // Créer le rendez-vous
    $rendezVous = RendezVous::create([
        'proposition_id' => $request->proposition_id,
        'creneau_id' => $creneau->id,
        'particulier_id' => $proposition->particulier_id,
        'agence_id' => $proposition->agence_id,
        'date_visite' => $creneau->date,
        'heure_visite' => $creneau->heure_debut,
        'statut' => StatutRendezVousEnum::PLANIFIE,
    ]);

    // Notifier l'agence
    try {
        $rendezVous->agence->user->notify(new RendezVousDemandeNotification($rendezVous));
    } catch (\Exception $e) {
        \Log::error('Erreur envoi notification rendez-vous: ' . $e->getMessage());
    }

    return redirect()->route('particulier.rendezvous.index')
        ->with('success', 'Visite planifiée avec succès.');
}

    public function confirmer(RendezVous $rendezVous)
    {
        if ($rendezVous->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $rendezVous->update(['statut' => StatutRendezVousEnum::CONFIRME]);

        // Notifier l'agence
        try {
            $rendezVous->agence->user->notify(new RendezVousConfirmeNotification($rendezVous));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi notification confirmation: ' . $e->getMessage());
        }

        return redirect()->route('particulier.rendezvous.index')
            ->with('success', 'Rendez-vous confirmé avec succès.');
    }

    public function annuler(RendezVous $rendezVous)
    {
        if ($rendezVous->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        // Réactiver le créneau
        if ($rendezVous->creneau_id) {
            $creneau = CreneauRendezVous::find($rendezVous->creneau_id);
            if ($creneau) {
                $creneau->update(['est_disponible' => true]);
            }
        }

        $rendezVous->update(['statut' => StatutRendezVousEnum::ANNULE]);

        return redirect()->route('particulier.rendezvous.index')
            ->with('success', 'Rendez-vous annulé avec succès.');
    }
}