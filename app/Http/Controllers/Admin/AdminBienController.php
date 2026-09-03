<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BienImmobilier;
use Illuminate\Http\Request;

class AdminBienController extends Controller
{
    public function index(Request $request)
    {
        $query = BienImmobilier::with(['agence.user', 'medias', 'quartier']);

        // Filtres
        if ($request->filled('filtre')) {
            switch ($request->filtre) {
                case 'disponibles':
                    $query->where('statut', true);
                    break;
                case 'indisponibles':
                    $query->where('statut', false);
                    break;
            }
        }

        // Filtre vedette (pour la page "À la une")
        if ($request->has('vedette')) {
            $query->vedette();
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhereHas('agence', function ($sub) use ($search) {
                      $sub->where('nom_agence', 'like', "%{$search}%");
                  });
            });
        }

        $biens = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => BienImmobilier::count(),
            'disponibles' => BienImmobilier::where('statut', true)->count(),
            'indisponibles' => BienImmobilier::where('statut', false)->count(),
            'en_vedette' => BienImmobilier::vedette()->count(),
        ];

        return view('admin.biens.index', compact('biens', 'stats'));
    }

    /**
     * Affiche le détail d'un bien - UTILISE LE SLUG
     */
    public function show(BienImmobilier $bien)
    {
        $bien->load([
            'agence.user', 
            'medias',
            'quartier', 
            'propositions.agence.user'
        ]);
        
        return view('admin.biens.show', compact('bien'));
    }

    /**
     * Mettre un bien en vedette - UTILISE LE SLUG
     */
    public function mettreEnVedette(Request $request, BienImmobilier $bien)
    {
        $request->validate([
            'duree' => 'required|integer|min:1|max:30',
        ]);

        // Vérifier si le bien est déjà en vedette
        if ($bien->est_vedette && $bien->vedette_fin > now()) {
            return redirect()->route('admin.biens.index')
                ->with('error', 'Ce bien est déjà en vedette jusqu\'au ' . $bien->vedette_fin->format('d/m/Y'));
        }

        $duree = (int) $request->duree;

        $bien->update([
            'est_vedette' => true,
            'vedette_debut' => now(),
            'vedette_fin' => now()->addDays($duree),
            'vedette_duree' => $duree,
        ]);

        return redirect()->route('admin.biens.index')
            ->with('success', ' Bien mis en vedette pour ' . $duree . ' jours.');
    }

    /**
     * Retirer un bien de la vedette - UTILISE LE SLUG
     */
    public function retirerVedette(BienImmobilier $bien)
    {
        $bien->update([
            'est_vedette' => false,
            'vedette_fin' => null,
        ]);

        return redirect()->route('admin.biens.index')
            ->with('success', '✅ Bien retiré de la vedette.');
    }

    /**
     * Prolonger la vedette d'un bien - UTILISE LE SLUG
     */
    public function prolongerVedette(Request $request, BienImmobilier $bien)
    {
        $request->validate([
            'duree' => 'required|integer|min:1|max:30',
        ]);

        if (!$bien->est_vedette) {
            return redirect()->route('admin.biens.index')
                ->with('error', 'Ce bien n\'est pas en vedette.');
        }

        $duree = (int) $request->duree;

        // Si vedette_fin est null, utiliser now()
        $dateFin = $bien->vedette_fin ?? now();
        $nouvelleFin = $dateFin->addDays($duree);

        $bien->update([
            'vedette_fin' => $nouvelleFin,
            'vedette_duree' => ($bien->vedette_duree ?? 0) + $duree,
        ]);

        return redirect()->route('admin.biens.index')
            ->with('success', '✅ Vedette prolongée de ' . $duree . ' jours.');
    }

    /**
     * Liste des biens en vedette
     */
    public function vedette()
    {
        $biens = BienImmobilier::with(['agence.user', 'medias', 'quartier'])
            ->vedette()
            ->orderBy('vedette_fin', 'asc')
            ->paginate(20);

        $stats = [
            'total' => BienImmobilier::count(),
            'en_vedette' => BienImmobilier::vedette()->count(),
            'expirees' => BienImmobilier::vedetteExpire()->count(),
        ];

        return view('admin.biens.vedette', compact('biens', 'stats'));
    }

    /**
     * Désactive un bien - UTILISE LE SLUG
     */
    public function desactiver(BienImmobilier $bien)
    {
        $bien->update(['statut' => false]);
        return redirect()->route('admin.biens.index')
            ->with('success', 'Bien désactivé avec succès.');
    }

    /**
     * Active un bien - UTILISE LE SLUG
     */
    public function activer(BienImmobilier $bien)
    {
        $bien->update(['statut' => true]);
        return redirect()->route('admin.biens.index')
            ->with('success', 'Bien activé avec succès.');
    }

    /**
     * Supprime un bien - UTILISE LE SLUG
     */
    public function destroy(BienImmobilier $bien)
    {
        // Supprimer les médias associés
        foreach ($bien->medias as $media) {
            if (file_exists(storage_path('app/public/' . $media->fichier))) {
                unlink(storage_path('app/public/' . $media->fichier));
            }
            $media->delete();
        }
        
        $bien->delete();
        return redirect()->route('admin.biens.index')
            ->with('success', 'Bien supprimé avec succès.');
    }
}