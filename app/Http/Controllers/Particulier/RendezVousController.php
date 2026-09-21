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
use App\Enums\StatutDemandeEnum;
use Illuminate\Support\Facades\DB;
use App\Notifications\RendezVousConfirmeNotification;
use App\Notifications\RendezVousDemandeNotification;

class RendezVousController extends Controller
{
    /**
     * Liste des rendez-vous actifs du particulier
     */
/**
 * Liste des rendez-vous du particulier
 * → RDV actifs + RDV clôturés récemment (7 jours)
 */
public function index()
{
    $particulier = Auth::user()->particulier;

    $rendezVous = RendezVous::with(['proposition.demande', 'proposition.bien', 'agence.user'])
        ->where('particulier_id', $particulier->id)
        ->where(function ($query) {
            // 1️⃣ RDV actifs
            $query->whereIn('statut', [
                StatutRendezVousEnum::PLANIFIE->value,
                StatutRendezVousEnum::CONFIRME->value,
            ])
            // 2️⃣ RDV clôturés récemment (7 jours) → pour voir la validation
            ->orWhere(function ($q) {
                $q->whereIn('statut', [
                    StatutRendezVousEnum::TERMINE->value,
                    StatutRendezVousEnum::CLIENT_ABSENT->value,
                ])
                ->where('updated_at', '>=', now()->subDays(7));
            });
        })
        ->orderByRaw("CASE statut 
            WHEN 'planifie' THEN 1 
            WHEN 'confirme' THEN 2 
            WHEN 'termine' THEN 3 
            WHEN 'client_absent' THEN 4 
            ELSE 5 
        END")
        ->orderBy('date_visite', 'asc')
        ->paginate(10);

    // Stats globales
    $baseQuery = RendezVous::where('particulier_id', $particulier->id);

    $stats = [
        'total'         => (clone $baseQuery)->count(),
        'planifie'      => (clone $baseQuery)->where('statut', StatutRendezVousEnum::PLANIFIE->value)->count(),
        'confirme'      => (clone $baseQuery)->where('statut', StatutRendezVousEnum::CONFIRME->value)->count(),
        'termine'       => (clone $baseQuery)->where('statut', StatutRendezVousEnum::TERMINE->value)->count(),
        'client_absent' => (clone $baseQuery)->where('statut', StatutRendezVousEnum::CLIENT_ABSENT->value)->count(),
    ];

    return view('particulier.rendezvous.index', compact('rendezVous', 'stats'));
}

    /**
     * Détails d'un rendez-vous
     */
    public function show(RendezVous $rendezVous)
    {
        if ($rendezVous->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $rendezVous->load(['proposition.demande', 'proposition.bien', 'agence.user']);

        return view('particulier.rendezvous.show', compact('rendezVous'));
    }

    /**
     * Affiche la page de choix des créneaux pour une proposition
     */
public function create(Proposition $proposition)
{
    if ($proposition->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    $statutValue = is_object($proposition->statut)
        ? $proposition->statut->value
        : (string) $proposition->statut;

    // ✅ Accepter en_attente ET acceptee
    if (!in_array($statutValue, [
        StatutPropositionEnum::EN_ATTENTE->value,
        StatutPropositionEnum::ACCEPTEE->value,
    ])) {
        return redirect()
            ->route('particulier.propositions.index')
            ->with('error', 'Cette proposition n\'est plus disponible pour une visite.');
    }

    // ✅ Vérifier qu'aucun RDV ACTIF n'existe déjà
    $aRdvActif = $proposition->rendezVous()
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value,
        ])
        ->exists();

    if ($aRdvActif) {
        return redirect()
            ->route('particulier.rendezvous.index')
            ->with('info', 'Une visite est déjà programmée pour cette offre.');
    }

    if ($proposition->agence) {
        $this->assurerCreneauxAgence($proposition->agence);
    }

    $creneaux = CreneauRendezVous::where('agence_id', $proposition->agence_id)
        ->where('est_disponible', true)
        ->where(function ($query) {
            $query->where('date', '>', now()->toDateString())
                ->orWhere(function ($q) {
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
                $heureDebut = strlen($planning['heure_debut']) === 5
                    ? $planning['heure_debut']
                    : substr($planning['heure_debut'], 0, 5);

                $heureFin = strlen($planning['heure_fin']) === 5
                    ? $planning['heure_fin']
                    : substr($planning['heure_fin'], 0, 5);

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

    /**
     * Enregistre la demande de visite
     */
 public function store(RendezVousRequest $request)
{
    $proposition = Proposition::findOrFail($request->proposition_id);

    if ($proposition->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    // ✅ Vérifier qu'aucun RDV ACTIF n'existe (planifie ou confirme)
    $aRdvActif = $proposition->rendezVous()
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value,
        ])
        ->exists();

    if ($aRdvActif) {
        return redirect()
            ->route('particulier.rendezvous.index')
            ->with('info', 'Une visite est déjà programmée pour cette offre.');
    }

    $statutValue = is_object($proposition->statut)
        ? $proposition->statut->value
        : (string) $proposition->statut;

    if (!in_array($statutValue, [
        StatutPropositionEnum::EN_ATTENTE->value,
        StatutPropositionEnum::ACCEPTEE->value,
    ])) {
        return back()->with('error', 'Cette proposition n\'est plus disponible pour une visite.');
    }

    // Récupérer le créneau
    $creneau = CreneauRendezVous::findOrFail($request->creneau_id);

    $creneauDateTime = \Carbon\Carbon::parse($creneau->date)
        ->setTimeFromTimeString($creneau->heure_debut);

    if (!$creneau->est_disponible || $creneauDateTime->isPast()) {
        return back()->with('error', 'Ce créneau n\'est plus disponible.');
    }

    if ($creneau->agence_id !== $proposition->agence_id) {
        return back()->with('error', 'Créneau invalide pour cette proposition.');
    }

    // Marquer le créneau comme indisponible
    $creneau->update(['est_disponible' => false]);

    // ✅ Créer le rendez-vous AVEC génération du code de visite
    $rendezVous = RendezVous::create([
        'proposition_id' => $request->proposition_id,
        'creneau_id'     => $creneau->id,
        'particulier_id' => $proposition->particulier_id,
        'agence_id'      => $proposition->agence_id,
        'date_visite'    => $creneau->date,
        'heure_visite'   => $creneau->heure_debut,
        'statut'         => StatutRendezVousEnum::PLANIFIE,
        'code_visite'    => RendezVous::genererCode(),
    ]);

    // Notifier l'agence
    try {
        $rendezVous->agence->user->notify(new RendezVousDemandeNotification($rendezVous));
    } catch (\Exception $e) {
        \Log::error('Erreur envoi notification rendez-vous: ' . $e->getMessage());
    }

    return redirect()
        ->route('particulier.rendezvous.index')
        ->with('success', 'Votre demande de visite a été envoyée à l\'agence.');
}
    /**
     * Confirmer un rendez-vous (côté particulier)
     */
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

        return redirect()
            ->route('particulier.rendezvous.index')
            ->with('success', 'Rendez-vous confirmé avec succès.');
    }

    /**
     * Annuler un rendez-vous
     */
public function annuler(RendezVous $rendezVous)
{
    if ($rendezVous->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    $statutValue = is_object($rendezVous->statut)
        ? $rendezVous->statut->value
        : (string) $rendezVous->statut;

    if ($statutValue === StatutRendezVousEnum::TERMINE->value) {
        return back()->with('error', 'Impossible d\'annuler une visite déjà effectuée.');
    }

    if (in_array($statutValue, [
        StatutRendezVousEnum::ANNULE->value,
        StatutRendezVousEnum::CLIENT_ABSENT->value,
    ])) {
        return back()->with('error', 'Ce rendez-vous est déjà clôturé.');
    }

    try {
        DB::beginTransaction();

        // Libérer le créneau
        if ($rendezVous->creneau_id) {
            $creneau = CreneauRendezVous::find($rendezVous->creneau_id);
            if ($creneau) {
                $creneau->update(['est_disponible' => true]);
            }
        }

        // RDV → annulé
        $rendezVous->update(['statut' => StatutRendezVousEnum::ANNULE->value]);

        // Reset proposition + demande + autres propositions
        $proposition = $rendezVous->proposition;
        if ($proposition) {
            $demande = $proposition->demande;

            if ($demande && $demande->statut === StatutDemandeEnum::EN_COURS->value) {
                $demande->update(['statut' => StatutDemandeEnum::EN_ATTENTE->value]);
            }

            $proposition->update([
                'statut'            => StatutPropositionEnum::EN_ATTENTE->value,
                'refus_automatique' => false,
            ]);

            Proposition::where('demande_id', $proposition->demande_id)
                ->where('id', '!=', $proposition->id)
                ->where('statut', StatutPropositionEnum::REFUSEE->value)
                ->where('refus_automatique', true)
                ->update([
                    'statut'            => StatutPropositionEnum::EN_ATTENTE->value,
                    'refus_automatique' => false,
                ]);
        }

        // ✅ NOUVEAU : Notifier l'agence que le client a annulé
        try {
            if ($rendezVous->agence?->user) {
                $rendezVous->agence->user->notify(
                    new \App\Notifications\RendezVousAnnuleNotification($rendezVous)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification annulation agence: ' . $e->getMessage());
        }

        DB::commit();

        return redirect()
            ->route('particulier.rendezvous.index')
            ->with('success', 'Rendez-vous annulé avec succès. Vous pouvez choisir une nouvelle offre.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur annulation RDV: ' . $e->getMessage());
        return redirect()
            ->route('particulier.rendezvous.index')
            ->with('error', 'Une erreur est survenue.');
    }
}
}