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
use App\Enums\TypeMediaEnum;
use App\Notifications\NouvellePropositionNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropositionAgenceController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;
        
        $propositions = Proposition::with(['demande.particulier.user', 'bien'])
            ->where('agence_id', $agence->id)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('agence.propositions.index', compact('propositions'));
    }

    public function create(DemandeImmobiliere $demande)
    {
        $agence = Auth::user()->agence;

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

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // ✅ Validation - prix_propose est maintenant nullable
            $validated = $request->validate([
                'demande_id' => 'required|exists:demande_immobilieres,id',
                'bien_id' => 'nullable|exists:biens_immobiliers,id',
                'prix_propose' => 'nullable|numeric|min:0',
                'message' => 'required|string|min:10|max:1000',
                'proposition_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
                'proposition_videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
                'proposition_images' => 'nullable|array|max:10',
                'proposition_videos' => 'nullable|array|max:1',
            ]);

            $agence = Auth::user()->agence;
            $demande = DemandeImmobiliere::findOrFail($request->demande_id);

            // ✅ Vérifier si l'agence a déjà fait une proposition pour cette demande
            $propositionExistante = Proposition::where('demande_id', $demande->id)
                ->where('agence_id', $agence->id)
                ->exists();

            if ($propositionExistante) {
                return redirect()->back()->with('error', 'Vous avez déjà fait une proposition pour cette demande.');
            }

            $hasBien = !empty($request->bien_id);
            $hasImages = $request->hasFile('proposition_images') && count($request->file('proposition_images')) > 0;
            $hasVideos = $request->hasFile('proposition_videos') && count($request->file('proposition_videos')) > 0;
            $hasMedia = $hasImages || $hasVideos;

            // ✅ Vérifier qu'on a soit un bien, soit des médias
            if (!$hasBien && !$hasMedia) {
                return redirect()->back()->with('error', 'Veuillez sélectionner un bien existant OU ajouter des photos/vidéos.');
            }

            // ✅ Vérifier qu'on n'a pas les deux en même temps
            if ($hasBien && $hasMedia) {
                return redirect()->back()->with('error', 'Vous ne pouvez pas sélectionner un bien ET ajouter des médias en même temps.');
            }

            // ✅ Gestion du bien
            $bienId = $request->bien_id;

            if (!$hasBien && $hasMedia) {
                // Créer un bien temporaire
                $tempBien = BienImmobilier::create([
                    'agence_id' => $agence->id,
                    'titre' => 'Proposition - ' . $demande->type_bien->label(),
                    'type_bien' => $demande->type_bien->value,
                    'type_contrat' => $demande->type_operation->value,
                    'prix' => $request->prix_propose ?? $demande->budget_maximum,
                    'surface' => $demande->surface_minimum ?? 0,
                    'quartier' => $demande->zone_recherchee,
                    'adresse' => $demande->zone_recherchee,
                    'nombre_chambres' => $demande->nombre_chambres ?? 1,
                    'nombre_salles_bain' => $demande->nombre_salles_bain ?? 1,
                    'statut' => false,
                    'est_vedette' => false,
                    'parking_disponible' => false,
                    'est_meuble' => false,
                    'description' => $request->message,
                ]);
                $bienId = $tempBien->id;
                Log::info('Bien temporaire créé pour la proposition', ['bien_id' => $bienId]);
            }

            // ✅ Récupérer le prix proposé
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

            // ✅ Création de la proposition
            $proposition = Proposition::create([
                'demande_id' => $request->demande_id,
                'agence_id' => $agence->id,
                'bien_id' => $bienId,
                'particulier_id' => $demande->particulier_id,
                'prix_propose' => $prixPropose,
                'message' => $request->message,
                'statut' => StatutPropositionEnum::EN_ATTENTE->value,
            ]);

            Log::info('Proposition créée avec succès', ['proposition_id' => $proposition->id]);

            // ✅ Upload des photos
            if ($request->hasFile('proposition_images')) {
                $images = $request->file('proposition_images');
                $images = array_filter($images, function($image) {
                    return $image && $image->isValid();
                });
                $images = array_slice($images, 0, 10);
                
                foreach ($images as $image) {
                    try {
                        $path = $image->store('propositions/' . $proposition->id . '/images', 'public');
                        Media::create([
                            'agence_id' => $agence->id,
                            'mediable_id' => $proposition->id,
                            'mediable_type' => Proposition::class,
                            'type_media' => TypeMediaEnum::IMAGE->value,
                            'fichier' => $path,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erreur téléchargement image proposition', ['error' => $e->getMessage()]);
                    }
                }
            }

            // ✅ Upload des vidéos
            if ($request->hasFile('proposition_videos')) {
                $videos = $request->file('proposition_videos');
                $videos = array_filter($videos, function($video) {
                    return $video && $video->isValid();
                });
                $video = reset($videos);
                
                if ($video) {
                    try {
                        $path = $video->store('propositions/' . $proposition->id . '/videos', 'public');
                        Media::create([
                            'agence_id' => $agence->id,
                            'mediable_id' => $proposition->id,
                            'mediable_type' => Proposition::class,
                            'type_media' => TypeMediaEnum::VIDEO->value,
                            'fichier' => $path,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Erreur téléchargement vidéo proposition', ['error' => $e->getMessage()]);
                    }
                }
            }

            DB::commit();

            // ✅ Notifier le particulier
            try {
                $demande->particulier->user->notify(new NouvellePropositionNotification($proposition));
            } catch (\Exception $e) {
                Log::error('Erreur envoi notification proposition: ' . $e->getMessage());
            }

            return redirect()->route('agence.propositions.index')
                ->with('success', 'Proposition envoyée avec succès !');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création de la proposition', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Proposition $proposition)
    {
        if ($proposition->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $proposition->load([
            'demande.particulier.user', 
            'bien.medias',
            'medias'
        ]);
        
        return view('agence.propositions.show', compact('proposition'));
    }

    public function annuler(Proposition $proposition)
    {
        if ($proposition->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        if ($proposition->statut !== StatutPropositionEnum::EN_ATTENTE->value) {
            return back()->with('error', 'Cette proposition ne peut plus être annulée.');
        }

        $proposition->update(['statut' => StatutPropositionEnum::REFUSEE->value]);

        return redirect()->route('agence.propositions.index')
            ->with('success', 'Proposition annulée avec succès.');
    }
}