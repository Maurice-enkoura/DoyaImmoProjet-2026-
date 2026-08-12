<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropositionRequest;
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

class PropositionAgenceController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;
        $propositions = Proposition::with(['demande.particulier.user', 'bien'])
            ->where('agence_id', $agence->id)
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
    // Validation
    $request->validate([
        'demande_id' => 'required|exists:demande_immobilieres,id',
        'bien_id' => 'nullable|exists:biens_immobiliers,id',
        'prix_propose' => 'required|numeric|min:0',
        'message' => 'required|string|min:10|max:1000',
        'proposition_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        'proposition_videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
    ]);

    $agence = Auth::user()->agence;
    $demande = DemandeImmobiliere::findOrFail($request->demande_id);

    // Vérifier si l'agence a déjà fait une proposition pour cette demande
    $propositionExistante = Proposition::where('demande_id', $demande->id)
        ->where('agence_id', $agence->id)
        ->exists();

    if ($propositionExistante) {
        return back()->with('error', 'Vous avez déjà fait une proposition pour cette demande.');
    }

    $hasBien = !empty($request->bien_id);
    $hasImages = $request->hasFile('proposition_images') && count($request->file('proposition_images')) > 0;
    $hasVideos = $request->hasFile('proposition_videos') && count($request->file('proposition_videos')) > 0;
    $hasMedia = $hasImages || $hasVideos;

    if (!$hasBien && !$hasMedia) {
        return back()->with('error', 'Veuillez sélectionner un bien existant OU ajouter des photos/vidéos.');
    }

    if ($hasBien && $hasMedia) {
        return back()->with('error', 'Vous ne pouvez pas sélectionner un bien ET ajouter des médias en même temps.');
    }

    // ✅ Si un bien est sélectionné, l'utiliser
    // ✅ Si aucun bien n'est sélectionné, créer un bien temporaire
    $bienId = $request->bien_id;

    if (!$hasBien && $hasMedia) {
        // Créer un bien temporaire pour la proposition
        $tempBien = BienImmobilier::create([
            'agence_id' => $agence->id,
            'titre' => 'Proposition sans bien - ' . $demande->type_bien->label(),
            'type_bien' => $demande->type_bien->value,
            'type_contrat' => $demande->type_operation->value,
            'prix' => $request->prix_propose,
            'surface' => $demande->surface_minimum ?? 0,
            'quartier' => $demande->zone_recherchee,
            'adresse' => $demande->zone_recherchee,
            'nombre_chambres' => $demande->nombre_chambres ?? 1,
            'nombre_salles_bain' => $demande->nombre_salles_bain ?? 1,
            'statut' => false, // Non visible sur la plateforme
            'est_vedette' => false,
            'parking_disponible' => false,
            'est_meuble' => false,
            'description' => $request->message,
        ]);
        $bienId = $tempBien->id;
    }

    // ✅ Création de la proposition
    $proposition = Proposition::create([
        'demande_id' => $request->demande_id,
        'agence_id' => $agence->id,
        'bien_id' => $bienId, // Maintenant toujours non-null
        'particulier_id' => $demande->particulier_id,
        'prix_propose' => $request->prix_propose,
        'message' => $request->message,
        'statut' => StatutPropositionEnum::EN_ATTENTE,
    ]);

    // Upload des photos de la proposition
    if ($request->hasFile('proposition_images')) {
        foreach ($request->file('proposition_images') as $image) {
            if ($image && $image->isValid()) {
                $path = $image->store('propositions/' . $proposition->id . '/images', 'public');
                Media::create([
                    'agence_id' => $agence->id,
                    'mediable_id' => $proposition->id,
                    'mediable_type' => Proposition::class,
                    'type_media' => TypeMediaEnum::IMAGE->value,
                    'fichier' => $path,
                ]);
            }
        }
    }

    // Upload des vidéos de la proposition
    if ($request->hasFile('proposition_videos')) {
        foreach ($request->file('proposition_videos') as $video) {
            if ($video && $video->isValid()) {
                $path = $video->store('propositions/' . $proposition->id . '/videos', 'public');
                Media::create([
                    'agence_id' => $agence->id,
                    'mediable_id' => $proposition->id,
                    'mediable_type' => Proposition::class,
                    'type_media' => TypeMediaEnum::VIDEO->value,
                    'fichier' => $path,
                ]);
            }
        }
    }

    // Notifier le particulier
    try {
        $demande->particulier->user->notify(new NouvellePropositionNotification($proposition));
    } catch (\Exception $e) {
        \Log::error('Erreur envoi notification proposition: ' . $e->getMessage());
    }

    return redirect()->route('agence.propositions.index')
        ->with('success', 'Proposition envoyée avec succès.');
}

    public function show(Proposition $proposition)
{
    if ($proposition->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    // ✅ Charger les relations correctement
    $proposition->load([
        'demande.particulier.user', 
        'bien.medias',      // Médias du bien
        'medias'            // ✅ Médias de la proposition (relation définie)
    ]);
    
    return view('agence.propositions.show', compact('proposition'));
}

    public function annuler(Proposition $proposition)
    {
        if ($proposition->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        if ($proposition->statut !== StatutPropositionEnum::EN_ATTENTE) {
            return back()->with('error', 'Cette proposition ne peut plus être annulée.');
        }

        $proposition->update(['statut' => StatutPropositionEnum::REFUSEE]);

        return redirect()->route('agence.propositions.index')
            ->with('success', 'Proposition annulée avec succès.');
    }
}