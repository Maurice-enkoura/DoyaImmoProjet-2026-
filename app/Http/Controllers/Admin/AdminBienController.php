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

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%");
            });
        }

        $biens = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => BienImmobilier::count(),
            'disponibles' => BienImmobilier::where('statut', true)->count(),
            'indisponibles' => BienImmobilier::where('statut', false)->count(),
        ];

        return view('admin.biens.index', compact('biens', 'stats'));
    }

    public function show(BienImmobilier $bien)
    {
        // Charger les relations sans order by est_principal
        $bien->load([
            'agence.user', 
            'medias', // On charge simplement les médias
            'quartier', 
            'propositions.agence.user'
        ]);
        
        return view('admin.biens.show', compact('bien'));
    }

    public function desactiver(BienImmobilier $bien)
    {
        $bien->update(['statut' => false]);
        return redirect()->route('admin.biens.index')
            ->with('success', 'Bien désactivé avec succès.');
    }

    public function activer(BienImmobilier $bien)
    {
        $bien->update(['statut' => true]);
        return redirect()->route('admin.biens.index')
            ->with('success', 'Bien activé avec succès.');
    }

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