<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\Models\DemandeImmobiliere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Enums\StatutPropositionEnum;
use Illuminate\Support\Facades\DB;
use App\Models\RendezVous;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Support\Facades\Notification;
use App\Enums\StatutDemandeEnum;
use App\Notifications\PropositionAccepteeNotification;
use App\Notifications\PropositionRefuseeNotification;
use App\Notifications\PropositionAcceptationAnnuleeNotification;
use App\Notifications\PropositionReactiveeNotification;

class PropositionController extends Controller
{
    /**
     * Affiche la liste des propositions du particulier
     */
    public function index()
    {
        $propositions = Proposition::with(['demande.particulier.user', 'agence.user', 'bien'])
            ->where('particulier_id', Auth::user()->particulier->id)
            ->whereHas('demande', function($query) {
                $query->whereIn('statut', [
                    StatutDemandeEnum::EN_ATTENTE->value,
                    StatutDemandeEnum::EN_COURS->value
                ]);
            })
            ->where(function($query) {
                $query->where('statut', StatutPropositionEnum::EN_ATTENTE->value)
                      ->whereDoesntHave('demande.propositions', function($subQ) {
                          $subQ->where('statut', StatutPropositionEnum::ACCEPTEE->value);
                      })
                      ->orWhere('statut', StatutPropositionEnum::ACCEPTEE->value);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.propositions.index', compact('propositions'));
    }

    /**
     * Affiche les détails d'une proposition
     */
    public function show(Proposition $proposition)
    {
        if ($proposition->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $proposition->load(['demande.particulier.user', 'agence.user', 'bien.medias', 'medias', 'rendezVous']);
        return view('particulier.propositions.show', compact('proposition'));
    }

    /**
     * Compare les propositions pour une demande
     */
    public function comparer(Request $request)
    {
        $demandeId = $request->demande_id;
        $demande = DemandeImmobiliere::with('particulier.user')->findOrFail($demandeId);

        $propositions = Proposition::with(['agence.user', 'bien'])
            ->where('demande_id', $demandeId)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value
            ])
            ->orderBy('prix_propose', 'asc')
            ->get();

        return view('particulier.propositions.comparer', compact('propositions', 'demande'));
    }

    /**
     * Sélectionne une proposition (le client l'accepte)
     */
    public function selectionner(Proposition $proposition)
{
    // ✅ 1. Vérifier l'appartenance
    if ($proposition->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    // ✅ 2. Statut de la proposition sécurisé
    $statutProposition = is_object($proposition->statut)
        ? $proposition->statut->value
        : (string) $proposition->statut;

    if ($statutProposition !== StatutPropositionEnum::EN_ATTENTE->value) {
        return back()->with('error', 'Cette proposition ne peut plus être sélectionnée (statut : ' . $statutProposition . ').');
    }

    // ✅ 3. Vérifier que la demande est encore active
    $demande = $proposition->demande;

    if (!$demande) {
        return back()->with('error', 'La demande associée est introuvable.');
    }

    $statutDemande = is_object($demande->statut)
        ? $demande->statut->value
        : (string) $demande->statut;

    if (!in_array($statutDemande, [
        StatutDemandeEnum::EN_ATTENTE->value,
        StatutDemandeEnum::EN_COURS->value,
    ])) {
        return back()->with('error', 'Cette demande n\'est plus active (statut : ' . $statutDemande . ').');
    }

    // ✅ 4. Vérifier qu'aucune proposition n'est déjà acceptée
    $hasAcceptee = $demande->propositions()
        ->where('statut', StatutPropositionEnum::ACCEPTEE->value)
        ->exists();

    if ($hasAcceptee) {
        return back()->with('error', 'Une proposition a déjà été acceptée pour cette demande.');
    }

    // ✅ 5. Vérifier qu'aucun RDV actif n'existe déjà sur cette proposition
    $hasRdvActif = $proposition->rendezVous()
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value,
        ])
        ->exists();

    if ($hasRdvActif) {
        return back()->with('error', 'Une visite est déjà programmée pour cette proposition.');
    }

    try {
        DB::beginTransaction();

        // ✅ 6. Mettre à jour la proposition sélectionnée
        $proposition->update([
            'statut'            => StatutPropositionEnum::ACCEPTEE->value,
            'refus_automatique' => false,
        ]);

        // ✅ 7. Refuser TOUTES les autres propositions de la demande (marquées AUTO)
        Proposition::where('demande_id', $proposition->demande_id)
            ->where('id', '!=', $proposition->id)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value,
            ])
            ->update([
                'statut'            => StatutPropositionEnum::REFUSEE->value,
                'refus_automatique' => true, // ✅ marqueur pour pouvoir les réactiver
            ]);

        // ✅ 8. Mettre à jour le statut de la demande
        $demande->update([
            'statut' => StatutDemandeEnum::EN_COURS->value,
        ]);

        // ✅ 9. Notifier l'agence
        try {
            if ($proposition->agence?->user) {
                $proposition->agence->user->notify(
                    new PropositionAccepteeNotification($proposition)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification: ' . $e->getMessage());
        }

        DB::commit();

        return redirect()
            ->route('particulier.propositions.index')
            ->with('success', 'Proposition sélectionnée avec succès. Les autres propositions ont été refusées.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur sélection proposition: ' . $e->getMessage());

        return back()->with('error', 'Une erreur est survenue lors de la sélection.');
    }
}

    /**
     * ✅ Refuser une proposition (le client la refuse VOLONTAIREMENT)
     */
    public function refuser(Proposition $proposition)
    {
        if ($proposition->particulier_id !== Auth::user()->particulier->id) {
            abort(403, 'Cette proposition ne vous appartient pas.');
        }

        if ($proposition->statut->value !== StatutPropositionEnum::EN_ATTENTE->value) {
            return back()->with('error', 'Cette proposition ne peut pas être refusée car elle n\'est plus en attente.');
        }

        $demande = $proposition->demande;
        $statutDemande = $demande->statut->value;

        if (!in_array($statutDemande, [
            StatutDemandeEnum::EN_ATTENTE->value,
            StatutDemandeEnum::EN_COURS->value
        ])) {
            return back()->with('error', 'Cette demande n\'est plus active.');
        }

        // ✅ Refus VOLONTAIRE → refus_automatique = false
        $proposition->update([
            'statut'            => StatutPropositionEnum::REFUSEE,
            'refus_automatique' => false,
        ]);

        try {
            $proposition->agence->user->notify(new PropositionRefuseeNotification($proposition));
        } catch (\Exception $e) {
            Log::error('Erreur notification refus: ' . $e->getMessage());
        }

        return redirect()->route('particulier.propositions.index')
            ->with('success', 'Offre refusée avec succès.');
    }

    /**
     * ✅ Annuler l'acceptation d'une proposition
     * Retour au statut en_attente + réactivation des refus automatiques
     */
    public function annulerSelection(Proposition $proposition)
    {
        // 1️⃣ Vérifier l'appartenance
        if ($proposition->particulier_id !== Auth::user()->particulier->id) {
            abort(403, 'Cette proposition ne vous appartient pas.');
        }

        // 2️⃣ Vérifier que la proposition est bien acceptée
        if ($proposition->statut->value !== StatutPropositionEnum::ACCEPTEE->value) {
            return back()->with('error', 'Cette proposition n\'est pas en statut accepté.');
        }

        // 3️⃣ Vérifier qu'aucun RDV n'existe (annulation possible uniquement AVANT planification)
        if ($proposition->rendezVous()->exists()) {
            return back()->with('error', 'Impossible d\'annuler : une visite a déjà été planifiée pour cette offre.');
        }

        $demande = $proposition->demande;

        // 4️⃣ Repasser la proposition acceptée à en_attente
        $proposition->update([
            'statut'            => StatutPropositionEnum::EN_ATTENTE,
            'refus_automatique' => false,
        ]);

        // 5️⃣ Réactiver UNIQUEMENT les propositions refusées automatiquement
        $propositionsReactivees = Proposition::where('demande_id', $demande->id)
            ->where('id', '!=', $proposition->id)
            ->where('statut', StatutPropositionEnum::REFUSEE->value)
            ->where('refus_automatique', true)
            ->get();

        foreach ($propositionsReactivees as $prop) {
            $prop->update([
                'statut'            => StatutPropositionEnum::EN_ATTENTE,
                'refus_automatique' => false,
            ]);

            // Notifier discrètement chaque agence dont l'offre est réactivée
            try {
                $prop->agence->user->notify(new PropositionReactiveeNotification($prop));
            } catch (\Exception $e) {
                Log::error('Erreur notification réactivation: ' . $e->getMessage());
            }
        }

        // 6️⃣ La demande repasse en EN_ATTENTE
        $demande->update(['statut' => StatutDemandeEnum::EN_ATTENTE->value]);

        // 7️⃣ Notifier l'agence dont l'offre a été annulée
        try {
            $proposition->agence->user->notify(new PropositionAcceptationAnnuleeNotification($proposition));
        } catch (\Exception $e) {
            Log::error('Erreur notification annulation: ' . $e->getMessage());
        }

        // 8️⃣ Log admin (trace de supervision)
        Log::info('Acceptation annulée par le particulier', [
            'particulier_id'    => $proposition->particulier_id,
            'proposition_id'    => $proposition->id,
            'demande_id'        => $demande->id,
            'agence_id'         => $proposition->agence_id,
            'reactivees_count'  => $propositionsReactivees->count(),
        ]);

        return redirect()->route('particulier.propositions.index')
            ->with('success', 'Acceptation annulée. La demande est de nouveau en attente et les offres concernées ont été réactivées.');
    }

    /**
 * ✅ Choisir et planifier : accepte l'offre puis redirige vers la planification
 */
public function choisirEtPlanifier(Proposition $proposition)
{
    // 1. Vérifier l'appartenance
    if ($proposition->particulier_id !== Auth::user()->particulier->id) {
        abort(403);
    }

    // 2. Vérifier le statut
    $statutProposition = is_object($proposition->statut)
        ? $proposition->statut->value
        : (string) $proposition->statut;

    if ($statutProposition !== StatutPropositionEnum::EN_ATTENTE->value) {
        return back()->with('error', 'Cette proposition ne peut plus être sélectionnée (statut : ' . $statutProposition . ').');
    }

    $demande = $proposition->demande;

    if (!$demande) {
        return back()->with('error', 'La demande associée est introuvable.');
    }

    // 3. Vérifier que la demande est active
    $statutDemande = is_object($demande->statut)
        ? $demande->statut->value
        : (string) $demande->statut;

    if (!in_array($statutDemande, [
        StatutDemandeEnum::EN_ATTENTE->value,
        StatutDemandeEnum::EN_COURS->value,
    ])) {
        return back()->with('error', 'Cette demande n\'est plus active.');
    }

    // 4. Vérifier qu'aucune autre n'est acceptée
    $hasAcceptee = $demande->propositions()
        ->where('statut', StatutPropositionEnum::ACCEPTEE->value)
        ->exists();

    if ($hasAcceptee) {
        return back()->with('error', 'Une proposition a déjà été acceptée pour cette demande.');
    }

    try {
        DB::beginTransaction();

        // 5. Accepter la proposition
        $proposition->update([
            'statut'            => StatutPropositionEnum::ACCEPTEE->value,
            'refus_automatique' => false,
        ]);

        // 6. Refuser les autres (auto)
        Proposition::where('demande_id', $proposition->demande_id)
            ->where('id', '!=', $proposition->id)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value,
            ])
            ->update([
                'statut'            => StatutPropositionEnum::REFUSEE->value,
                'refus_automatique' => true,
            ]);

        // 7. Demande → en_cours
        $demande->update([
            'statut' => StatutDemandeEnum::EN_COURS->value,
        ]);

        // 8. Notifier l'agence
        try {
            if ($proposition->agence?->user) {
                $proposition->agence->user->notify(
                    new PropositionAccepteeNotification($proposition)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification: ' . $e->getMessage());
        }

        DB::commit();

        // 9. Redirection vers la page de planification
        return redirect()
            ->route('particulier.rendezvous.create', $proposition->id)
            ->with('success', 'Offre choisie ! Choisissez maintenant un créneau de visite.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur choisirEtPlanifier: ' . $e->getMessage());

        return back()->with('error', 'Une erreur est survenue.');
    }
}

}