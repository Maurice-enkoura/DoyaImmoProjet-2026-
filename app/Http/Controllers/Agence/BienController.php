<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Http\Requests\BienImmobilierRequest;
use App\Models\BienImmobilier;
use App\Models\Agence;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BienController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;
        $biens = BienImmobilier::where('agence_id', $agence->id)
            ->with('medias')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('agence.biens.index', compact('biens'));
    }

    public function create()
    {
        // Vérifier si l'agence peut encore publier des biens
        $agence = Auth::user()->agence;
        $abonnement = $agence->abonnementActif()->first();

        if (!$abonnement) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Vous devez souscrire un abonnement actif pour publier des biens.');
        }

        $limiteBiens = $abonnement->formule->limiteBiens();
        $biensActifs = $agence->biens()->where('statut', true)->count();

        if ($biensActifs >= $limiteBiens) {
            return redirect()->route('agence.biens.index')
                ->with('error', 'Vous avez atteint la limite de biens autorisés pour votre formule d\'abonnement.');
        }

        return view('agence.biens.create');
    }

    public function store(BienImmobilierRequest $request)
    {
        $agence = Auth::user()->agence;

        $bien = BienImmobilier::create([
            'agence_id' => $agence->id,
            'titre' => $request->titre,
            'type_bien' => $request->type_bien,
            'type_contrat' => $request->type_contrat,
            'prix' => $request->prix,
            'quartier' => $request->quartier,
            'adresse' => $request->adresse,
            'nombre_chambres' => $request->nombre_chambres,
            'nombre_salles_bain' => $request->nombre_salles_bain,
            'surface' => $request->surface,
            'parking_disponible' => $request->boolean('parking_disponible'),
            'est_meuble' => $request->boolean('est_meuble'),
            'description' => $request->description,
            'statut' => true,
        ]);

        // Télécharger les images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('biens/images', 'public');
                Media::create([
                    'agence_id' => $agence->id,
                    'mediable_id' => $bien->id,
                    'mediable_type' => BienImmobilier::class,
                    'type_media' => 'image',
                    'fichier' => $path,
                ]);
            }
        }

        // Télécharger les vidéos
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $path = $video->store('biens/videos', 'public');
                Media::create([
                    'agence_id' => $agence->id,
                    'mediable_id' => $bien->id,
                    'mediable_type' => BienImmobilier::class,
                    'type_media' => 'video',
                    'fichier' => $path,
                ]);
            }
        }

        return redirect()->route('agence.biens.index')
            ->with('success', 'Bien ajouté avec succès.');
    }

    public function show(BienImmobilier $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $bien->load(['medias', 'propositions.demande.particulier.user']);
        return view('agence.biens.show', compact('bien'));
    }

    public function edit(BienImmobilier $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        return view('agence.biens.edit', compact('bien'));
    }

    public function destroy(BienImmobilier $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $bien->update(['statut' => false]);

        return redirect()->route('agence.biens.index')
            ->with('success', 'Bien désactivé avec succès.');
    }

    public function activer(BienImmobilier $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $bien->update(['statut' => true]);

        return redirect()->route('agence.biens.index')
            ->with('success', 'Bien réactivé avec succès.');
    }

  public function update(Request $request, BienImmobilier $bien)
{
    if ($bien->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    $validated = $request->validate([
        'titre' => 'required|string|max:255',
        'type_bien' => 'required|string',
        'type_contrat' => 'required|string',
        'prix' => 'required|numeric|min:0',
        'quartier' => 'required|string|max:255',
        'adresse' => 'required|string|max:255',
        'nombre_chambres' => 'nullable|integer|min:0',
        'nombre_salles_bain' => 'nullable|integer|min:0',
        'surface' => 'required|numeric|min:0',
        'parking_disponible' => 'nullable|boolean',
        'est_meuble' => 'nullable|boolean',
        'description' => 'required|string|min:20',
        'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        'videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
    ]);

    $bien->update([
        'titre' => $request->titre,
        'type_bien' => $request->type_bien,
        'type_contrat' => $request->type_contrat,
        'prix' => $request->prix,
        'quartier' => $request->quartier,
        'adresse' => $request->adresse,
        'nombre_chambres' => $request->nombre_chambres ?? 0,
        'nombre_salles_bain' => $request->nombre_salles_bain ?? 0,
        'surface' => $request->surface,
        'parking_disponible' => $request->boolean('parking_disponible'),
        'est_meuble' => $request->boolean('est_meuble'),
        'description' => $request->description,
    ]);

    // Upload des images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            if ($image->isValid()) {
                $path = $image->store('biens/images', 'public');
                Media::create([
                    'agence_id' => Auth::user()->agence->id,
                    'mediable_id' => $bien->id,
                    'mediable_type' => BienImmobilier::class,
                    'type_media' => 'image',
                    'fichier' => $path,
                ]);
            }
        }
    }

    // Upload des vidéos
    if ($request->hasFile('videos')) {
        foreach ($request->file('videos') as $video) {
            if ($video->isValid()) {
                $path = $video->store('biens/videos', 'public');
                Media::create([
                    'agence_id' => Auth::user()->agence->id,
                    'mediable_id' => $bien->id,
                    'mediable_type' => BienImmobilier::class,
                    'type_media' => 'video',
                    'fichier' => $path,
                ]);
            }
        }
    }

    return redirect()->route('agence.biens.index')
        ->with('success', 'Bien mis à jour avec succès.');
}

    public function supprimerMedia(Media $media)
    {
        if ($media->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        // Supprimer le fichier physique
        if (Storage::disk('public')->exists($media->fichier)) {
            Storage::disk('public')->delete($media->fichier);
        }

        $media->delete();

        return redirect()->back()->with('success', 'Média supprimé avec succès.');
    }

    

}