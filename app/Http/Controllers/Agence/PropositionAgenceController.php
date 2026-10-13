<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\Agence;
use App\Models\BienImmobilier;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutDemandeEnum;
use App\Enums\TypeMediaEnum;
use App\Notifications\NouvellePropositionNotification;
use App\Notifications\PropositionAnnuleeParAgenceNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropositionAgenceController extends Controller
{
    /**
     * Liste des propositions - en_attente et acceptee
     */
    public function index(Request $request)
    {
        $agence = Auth::user()->agence;

        $query = Proposition::with(['demande.particulier.user', 'bien'])
            ->where('agence_id', $agence->id)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value,
            ]);

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $propositions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('agence.propositions.index', compact('propositions'));
    }

    /**
     * Formulaire de création d'une proposition
     */
    public function create(DemandeImmobiliere $demande)
    {
        $agence = Auth::user()->agence;

        if (!config('abonnement.actif', false) || config('abonnement.mode_gratuit', true)) {
            $biens = $agence->biens()->where('statut', true)->get();
            return view('agence.propositions.create', compact('demande', 'biens'));
        }

        if (!$agence->estValidee()) {
            return redirect()->route('agence.dashboard')
                ->with('error', 'Votre agence doit être validée pour faire des propositions.');
        }

        if (!$agence->aAbonnementActif()) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Vous devez avoir un abonnement actif pour faire des propositions.');
        }

        $biens = $agence->biens()->where('statut', true)->get();

        return view('agence.propositions.create', compact('demande', 'biens'));
    }

   /**
 * Enregistrer une nouvelle proposition
 */
public function store(Request $request)
{
    try {
        DB::beginTransaction();

        $validated = $request->validate([
            'demande_id'               => 'required|exists:demande_immobilieres,id',
            'bien_id'                  => 'nullable|exists:biens_immobiliers,id',
            'prix_propose'             => 'nullable|numeric|min:0',
            'disponibilite'            => 'nullable|date',
            'caution_mois'             => 'nullable|integer|min:0|max:12',
            'avance_loyer_mois'        => 'nullable|integer|min:0|max:12',
            'honoraires'               => 'nullable|numeric|min:0',
            'charges'                  => 'nullable|numeric|min:0',
            'conditions_particulieres' => 'nullable|string|max:2000',
            'message'                  => 'required|string|min:10|max:1000',
            'proposition_images.*'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'proposition_videos.*'     => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'proposition_images'       => 'nullable|array|max:10',
            'proposition_videos'       => 'nullable|array|max:1',
        ]);

        $agence  = Auth::user()->agence;
        $demande = DemandeImmobiliere::findOrFail($request->demande_id);

        $propositionExistante = Proposition::where('demande_id', $demande->id)
            ->where('agence_id', $agence->id)
            ->exists();

        if ($propositionExistante) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Vous avez déjà fait une proposition pour cette demande.');
        }

        $hasBien   = !empty($request->bien_id);
        $hasImages = $request->hasFile('proposition_images') && count($request->file('proposition_images')) > 0;
        $hasVideos = $request->hasFile('proposition_videos') && count($request->file('proposition_videos')) > 0;
        $hasMedia  = $hasImages || $hasVideos;

        if ($hasBien) {
            $bienSelect = BienImmobilier::find($request->bien_id);
            if (!$bienSelect || $bienSelect->agence_id !== $agence->id) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Le bien sélectionné n\'appartient pas à votre agence.')->withInput();
            }
        }

        if (!$hasBien && !$hasMedia) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Veuillez sélectionner un bien existant OU ajouter des photos/vidéos.');
        }

        if ($hasBien && $hasMedia) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Vous ne pouvez pas sélectionner un bien ET ajouter des médias en même temps.');
        }

        $bienId = $request->bien_id;

        if (!$hasBien && $hasMedia) {
            $tempBien = BienImmobilier::create([
                'agence_id'          => $agence->id,
                'titre'              => 'Proposition - ' . $demande->type_bien->label(),
                'type_bien'          => $demande->type_bien->value,
                'type_contrat'       => $demande->type_operation->value,
                'prix'               => $request->prix_propose ?? $demande->budget_maximum,
                'surface'            => $demande->surface_minimum ?? 0,
                'quartier'           => $demande->zone_recherchee,
                'adresse'            => $demande->zone_recherchee,
                'nombre_chambres'    => $demande->nombre_chambres ?? 1,
                'nombre_salles_bain' => $demande->nombre_salles_bain ?? 1,
                'statut'             => false,
                'est_vedette'        => false,
                'parking_disponible' => false,
                'est_meuble'         => false,
                'description'        => $request->message,
            ]);
            $bienId = $tempBien->id;
        }

        $prixPropose = $request->prix_propose;
        if (empty($prixPropose) && $hasBien) {
            $bien = BienImmobilier::find($bienId);
            if ($bien) {
                $prixPropose = $bien->prix;
            }
        }
        if (empty($prixPropose)) {
            $prixPropose = $demande->budget_maximum;
        }

        // ═══════════════════════════════════════════════════════════
        // ✅ Création de la proposition avec gestion PROPRE des null
        // ═══════════════════════════════════════════════════════════
        $proposition = Proposition::create([
            'demande_id'               => $request->demande_id,
            'agence_id'                => $agence->id,
            'bien_id'                  => $bienId,
            'particulier_id'           => $demande->particulier_id,
            'prix_propose'             => $prixPropose,

            // ✅ Disponibilité : convertit '' en null
            'disponibilite'            => $request->filled('disponibilite')
                                            ? $request->disponibilite
                                            : null,

            // ✅ Caution : convertit 0 ou '' en null
            'caution_mois'             => $request->filled('caution_mois') && (int) $request->caution_mois > 0
                                            ? (int) $request->caution_mois
                                            : null,

            // ✅ Avance loyer : convertit 0 ou '' en null
            'avance_loyer_mois'        => $request->filled('avance_loyer_mois') && (int) $request->avance_loyer_mois > 0
                                            ? (int) $request->avance_loyer_mois
                                            : null,

            // ✅ Honoraires : convertit 0 ou '' en null
            'honoraires'               => $request->filled('honoraires') && (int) $request->honoraires > 0
                                            ? (int) $request->honoraires
                                            : null,

            // ✅ Charges : convertit 0 ou '' en null
            'charges'                  => $request->filled('charges') && (int) $request->charges > 0
                                            ? (int) $request->charges
                                            : null,

            // ✅ Conditions particulières : convertit '' en null
            'conditions_particulieres' => $request->filled('conditions_particulieres')
                                            && trim($request->conditions_particulieres) !== ''
                                            ? trim($request->conditions_particulieres)
                                            : null,

            'message'                  => $request->message,
            'statut'                   => StatutPropositionEnum::EN_ATTENTE->value,
        ]);

        // Upload des médias
        if ($request->hasFile('proposition_images')) {
            $images = array_slice(array_filter($request->file('proposition_images'), fn($i) => $i && $i->isValid()), 0, 10);
            foreach ($images as $image) {
                try {
                    $path = \App\Services\ImageOptimizerService::storeAndOptimize($image, 'propositions/' . $proposition->id . '/images');
                    Media::create([
                        'agence_id'     => $agence->id,
                        'mediable_id'   => $proposition->id,
                        'mediable_type' => Proposition::class,
                        'type_media'    => TypeMediaEnum::IMAGE->value,
                        'fichier'       => $path,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur image: ' . $e->getMessage());
                }
            }
        }

        if ($request->hasFile('proposition_videos')) {
            $videos = array_filter($request->file('proposition_videos'), fn($v) => $v && $v->isValid());
            $video  = reset($videos);
            if ($video) {
                try {
                    $path = $video->store('propositions/' . $proposition->id . '/videos', 'public');
                    Media::create([
                        'agence_id'     => $agence->id,
                        'mediable_id'   => $proposition->id,
                        'mediable_type' => Proposition::class,
                        'type_media'    => TypeMediaEnum::VIDEO->value,
                        'fichier'       => $path,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Erreur vidéo: ' . $e->getMessage());
                }
            }
        }

        DB::commit();

        try {
            $demande->particulier->user->notify(new NouvellePropositionNotification($proposition));
        } catch (\Exception $e) {
            Log::error('Erreur notification proposition: ' . $e->getMessage());
        }

        return redirect()->route('agence.propositions.index')
            ->with('success', 'Proposition envoyée avec succès !');

    } catch (ValidationException $e) {
        DB::rollBack();
        return redirect()->back()->withErrors($e->validator)->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur création proposition: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage())->withInput();
    }
}

    /**
     * Détail d'une proposition
     */
    public function show(Proposition $proposition)
    {
        if ($proposition->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $proposition->load([
            'demande.particulier.user',
            'bien.medias',
            'medias',
            'rendezVous',
        ]);

        return view('agence.propositions.show', compact('proposition'));
    }

    /**
     * Annuler une proposition
     * ✅ Peut être appelée sur en_attente OU acceptee
     * ✅ Si acceptee → refuse + notifie le particulier + remet la demande en_attente
     */
    public function annuler(Proposition $proposition)
    {
        // Vérifier l'appartenance
        if ($proposition->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        // Récupérer la valeur string du statut
        $statutValue = $proposition->statut instanceof \BackedEnum
            ? $proposition->statut->value
            : (string) $proposition->statut;

        // ✅ Autoriser l'annulation sur en_attente ET acceptee
        if (!in_array($statutValue, [
            StatutPropositionEnum::EN_ATTENTE->value,
            StatutPropositionEnum::ACCEPTEE->value,
        ])) {
            return redirect()
                ->route('agence.propositions.index')
                ->with('error', 'Cette proposition ne peut plus être annulée (statut actuel : ' . $statutValue . ').');
        }

        // ✅ Vérifier qu'aucun RDV n'existe (sinon bloqué)
        if ($proposition->rendezVous()->exists()) {
            return redirect()
                ->route('agence.propositions.index')
                ->with('error', 'Impossible d\'annuler : une visite est déjà planifiée pour cette proposition.');
        }

        try {
            DB::beginTransaction();

            $etaitAcceptee = $statutValue === StatutPropositionEnum::ACCEPTEE->value;

            // Passer en refusée
            $proposition->update([
                'statut'            => StatutPropositionEnum::REFUSEE->value,
                'refus_automatique' => false,
            ]);

            // ✅ Si la proposition était acceptée → remettre la demande en attente
            if ($etaitAcceptee) {
                $demande = $proposition->demande;
                if ($demande) {
                    $demande->update(['statut' => StatutDemandeEnum::EN_ATTENTE->value]);
                }
            }

            // ✅ Notifier le particulier
            try {
                if ($proposition->particulier?->user) {
                    $proposition->particulier->user->notify(
                        new PropositionAnnuleeParAgenceNotification($proposition)
                    );
                }
            } catch (\Exception $e) {
                Log::error('Erreur notification annulation agence: ' . $e->getMessage());
            }

            DB::commit();

            $message = $etaitAcceptee
                ? 'Proposition annulée. La demande du particulier repasse en attente.'
                : 'Proposition annulée avec succès.';

            return redirect()
                ->route('agence.propositions.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur annulation proposition: ' . $e->getMessage());
            return redirect()
                ->route('agence.propositions.index')
                ->with('error', 'Erreur lors de l\'annulation.');
        }
    }
}