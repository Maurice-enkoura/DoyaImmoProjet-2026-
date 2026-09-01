<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Http\Requests\BienImmobilierRequest;
use App\Models\BienImmobilier;
use App\Models\Agence;
use App\Models\Media;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BienController extends Controller
{
    /**
     * Vérifier si l'agence peut publier des biens (abonnement Pro)
     */
    private function peutPublierBiens($agence): bool
    {
        $abonnement = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        return $abonnement && $abonnement->formule->value === 'pro';
    }

    /**
     * Vérifier si l'agence a un abonnement actif
     */
    private function getAbonnementActif($agence)
    {
        return $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
    }

    public function index()
    {
        $agence = Auth::user()->agence;
        
        // ✅ Vérifier si l'agence peut publier des biens
        $peutPublier = $this->peutPublierBiens($agence);
        
        $biens = BienImmobilier::where('agence_id', $agence->id)
            ->with('medias')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('agence.biens.index', compact('biens', 'peutPublier'));
    }

    public function create()
    {
        $agence = Auth::user()->agence;
        
        // ✅ Vérifier l'abonnement Pro
        if (!$this->peutPublierBiens($agence)) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Seules les agences avec un abonnement Pro peuvent publier des biens.');
        }

        return view('agence.biens.create');
    }

    public function store(BienImmobilierRequest $request)
    {
        $agence = Auth::user()->agence;

        // ✅ Vérifier l'abonnement Pro
        if (!$this->peutPublierBiens($agence)) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Seules les agences avec un abonnement Pro peuvent publier des biens.');
        }

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
            ->with('success', ' Bien ajouté avec succès.');
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

    /**
     * Supprimer définitivement un bien
     */
    public function destroy(BienImmobilier $bien)
    {
        try {
            $agence = Auth::user()->agence;
            
            if ($bien->agence_id !== $agence->id) {
                return redirect()->route('agence.biens.index')
                    ->with('error', 'Ce bien ne vous appartient pas.');
            }

            // ✅ Supprimer les médias associés
            foreach ($bien->medias as $media) {
                if (Storage::disk('public')->exists($media->fichier)) {
                    Storage::disk('public')->delete($media->fichier);
                }
                $media->delete();
            }

            // ✅ Supprimer définitivement le bien
            $bien->delete();

            return redirect()->route('agence.biens.index')
                ->with('success', 'Bien supprimé définitivement avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur suppression bien: ' . $e->getMessage());
            return redirect()->route('agence.biens.index')
                ->with('error', 'Erreur lors de la suppression du bien.');
        }
    }

    /**
     * Activer un bien
     */
    public function activer(BienImmobilier $bien)
    {
        try {
            if ($bien->agence_id !== Auth::user()->agence->id) {
                abort(403);
            }

            $bien->update(['statut' => true]);

            session()->forget('success');
            session()->forget('error');

            return redirect()->route('agence.biens.index')
                ->with('success', ' Bien réactivé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur activation bien: ' . $e->getMessage());
            return redirect()->route('agence.biens.index')
                ->with('error', 'Erreur lors de la réactivation du bien.');
        }
    }

    /**
     * Désactiver un bien
     */
    public function desactiver(BienImmobilier $bien)
    {
        try {
            if ($bien->agence_id !== Auth::user()->agence->id) {
                abort(403);
            }

            $bien->update(['statut' => false]);

            session()->forget('success');
            session()->forget('error');

            return redirect()->route('agence.biens.index')
                ->with('success', ' Bien désactivé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur désactivation bien: ' . $e->getMessage());
            
            session()->forget('success');
            session()->forget('error');

            return redirect()->route('agence.biens.index')
                ->with('error', 'Erreur lors de la désactivation du bien.');
        }
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
            ->with('success', ' Bien mis à jour avec succès.');
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

        return redirect()->back()->with('success', ' Média supprimé avec succès.');
    }
}