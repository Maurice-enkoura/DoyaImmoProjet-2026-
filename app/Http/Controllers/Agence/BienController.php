<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Http\Requests\BienImmobilierRequest;
use App\Http\Requests\UpdateBienRequest;  // ✅ Déjà importé
use App\Models\BienImmobilier;
use App\Models\Agence;
use App\Models\Media;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BienController extends Controller
{
    /**
     * Vérifier si l'agence peut publier des biens
     */
    private function peutPublierBiens($agence): bool
    {
        // ✅ Si mode gratuit ou abonnement désactivé, TOUJOURS autorisé
        if (!config('abonnement.actif', false) || config('abonnement.mode_gratuit', true)) {
            return true;
        }

        // ✅ Logique normale d'abonnement
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

    public function index(Request $request)
    {
        $agence = Auth::user()->agence;
        
        $peutPublier = $this->peutPublierBiens($agence);
        
        $query = BienImmobilier::where('agence_id', $agence->id)
            ->with('medias');

        // ✅ Filtre par statut
        if ($request->filled('statut')) {
            if ($request->statut === 'disponible') {
                $query->where('statut', true);
            } elseif ($request->statut === 'indisponible') {
                $query->where('statut', false);
            }
        }

        // ✅ Filtre par vedette
        if ($request->filled('vedette') && $request->vedette === 'true') {
            $query->where('est_vedette', true);
        }

        $biens = $query->orderBy('created_at', 'desc')->paginate(10);
            
        return view('agence.biens.index', compact('biens', 'peutPublier'));
    }

    public function create()
    {
        $agence = Auth::user()->agence;
        
        if (!$this->peutPublierBiens($agence)) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Seules les agences avec un abonnement Pro peuvent publier des biens.');
        }

        return view('agence.biens.create');
    }

    public function store(BienImmobilierRequest $request)
    {
        try {
            DB::beginTransaction();

            $agence = Auth::user()->agence;

            if (!$this->peutPublierBiens($agence)) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Seules les agences avec un abonnement Pro peuvent publier des biens.');
            }

            Log::info('Création de bien - Données reçues', [
                'agence_id' => $agence->id,
                'titre' => $request->titre,
                'has_images' => $request->hasFile('images'),
                'has_videos' => $request->hasFile('videos'),
            ]);

            // ✅ Générer le slug
            $slug = Str::slug($request->titre) . '-' . uniqid();

            // ✅ Créer le bien
            $bien = BienImmobilier::create([
                'agence_id' => $agence->id,
                'titre' => $request->titre,
                'slug' => $slug,
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
                'statut' => true,
                'climatisation' => $request->boolean('climatisation'),
                'balcon' => $request->boolean('balcon'),
                'jardin' => $request->boolean('jardin'),
                'piscine' => $request->boolean('piscine'),
                'ascenseur' => $request->boolean('ascenseur'),
                'securite' => $request->boolean('securite'),
            ]);

            Log::info('Bien créé avec succès', ['bien_id' => $bien->id]);

            // ✅ Télécharger les images (max 10)
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $images = array_filter($images, function($image) {
                    return $image && $image->isValid();
                });
                
                $images = array_slice($images, 0, 10);
                
                Log::info('Téléchargement des images', ['count' => count($images)]);
                
                foreach ($images as $index => $image) {
                    try {
                        $path = $image->store('biens/images', 'public');
                        Media::create([
                            'agence_id' => $agence->id,
                            'mediable_id' => $bien->id,
                            'mediable_type' => BienImmobilier::class,
                            'type_media' => 'image',
                            'fichier' => $path,
                        ]);
                        Log::info('Image téléchargée', ['index' => $index, 'path' => $path]);
                    } catch (\Exception $e) {
                        Log::error('Erreur téléchargement image', [
                            'index' => $index,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            // ✅ Télécharger les vidéos (max 1)
            if ($request->hasFile('videos')) {
                $videos = $request->file('videos');
                $videos = array_filter($videos, function($video) {
                    return $video && $video->isValid();
                });
                
                $video = reset($videos);
                
                if ($video) {
                    try {
                        $path = $video->store('biens/videos', 'public');
                        Media::create([
                            'agence_id' => $agence->id,
                            'mediable_id' => $bien->id,
                            'mediable_type' => BienImmobilier::class,
                            'type_media' => 'video',
                            'fichier' => $path,
                        ]);
                        Log::info('Vidéo téléchargée', ['path' => $path]);
                    } catch (\Exception $e) {
                        Log::error('Erreur téléchargement vidéo', [
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()->route('agence.biens.index')
                ->with('success', 'Bien ajouté avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du bien', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erreur lors de l\'ajout du bien: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Affiche le détail d'un bien - UTILISE LE SLUG
     */
    public function show(BienImmobilier $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $bien->load(['medias', 'propositions.demande.particulier.user']);
        return view('agence.biens.show', compact('bien'));
    }

    /**
     * Formulaire d'édition d'un bien - UTILISE LE SLUG
     */
    public function edit(BienImmobilier $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        return view('agence.biens.edit', compact('bien'));
    }

    /**
     * Supprime un bien - UTILISE LE SLUG
     */
    public function destroy(BienImmobilier $bien)
    {
        try {
            $agence = Auth::user()->agence;
            
            if ($bien->agence_id !== $agence->id) {
                return redirect()->route('agence.biens.index')
                    ->with('error', 'Ce bien ne vous appartient pas.');
            }

            foreach ($bien->medias as $media) {
                if (Storage::disk('public')->exists($media->fichier)) {
                    Storage::disk('public')->delete($media->fichier);
                }
                $media->delete();
            }

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
     * Active un bien - UTILISE LE SLUG
     */
    public function activer(BienImmobilier $bien)
    {
        try {
            if ($bien->agence_id !== Auth::user()->agence->id) {
                abort(403);
            }

            $bien->update(['statut' => true]);

            return redirect()->route('agence.biens.index')
                ->with('success', 'Bien réactivé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur activation bien: ' . $e->getMessage());
            return redirect()->route('agence.biens.index')
                ->with('error', 'Erreur lors de la réactivation du bien.');
        }
    }

    /**
     * Désactive un bien - UTILISE LE SLUG
     */
    public function desactiver(BienImmobilier $bien)
    {
        try {
            if ($bien->agence_id !== Auth::user()->agence->id) {
                abort(403);
            }

            $bien->update(['statut' => false]);

            return redirect()->route('agence.biens.index')
                ->with('success', 'Bien désactivé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur désactivation bien: ' . $e->getMessage());
            return redirect()->route('agence.biens.index')
                ->with('error', 'Erreur lors de la désactivation du bien.');
        }
    }

    /**
     * ✅ Met à jour un bien - UTILISE LE SLUG
     * ✅ Utilise UpdateBienRequest (sans règles d'images)
     */
    public function update(UpdateBienRequest $request, BienImmobilier $bien)  // ✅ ICI c'est UpdateBienRequest
    {
        if ($bien->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        // ✅ Mettre à jour le bien
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
            'climatisation' => $request->boolean('climatisation'),
            'balcon' => $request->boolean('balcon'),
            'jardin' => $request->boolean('jardin'),
            'piscine' => $request->boolean('piscine'),
            'ascenseur' => $request->boolean('ascenseur'),
            'securite' => $request->boolean('securite'),
        ]);

        // ✅ Upload des images UNIQUEMENT si des fichiers sont envoyés
        if ($request->hasFile('images')) {
            $images = array_filter($request->file('images'), function($image) {
                return $image && $image->isValid();
            });
            $images = array_slice($images, 0, 10);
            
            foreach ($images as $image) {
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

        // ✅ Upload des vidéos UNIQUEMENT si des fichiers sont envoyés
        if ($request->hasFile('videos')) {
            $videos = array_filter($request->file('videos'), function($video) {
                return $video && $video->isValid();
            });
            $video = reset($videos);
            
            if ($video) {
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

        return redirect()->route('agence.biens.index')
            ->with('success', 'Bien mis à jour avec succès.');
    }


    /**
     * ✅ Supprime un média - RETOURNE DU JSON
     */
    public function supprimerMedia(Media $media)
    {
        try {
            // ✅ Vérifier que l'agence est propriétaire du média
            if ($media->agence_id !== Auth::user()->agence->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action non autorisée.'
                ], 403);
            }

            // ✅ Supprimer le fichier physique
            if (Storage::disk('public')->exists($media->fichier)) {
                Storage::disk('public')->delete($media->fichier);
            }

            // ✅ Supprimer le média de la base de données
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Média supprimé avec succès.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur suppression média: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du média.'
            ], 500);
        }
    }
}