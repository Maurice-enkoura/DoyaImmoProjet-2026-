<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Http\Request;

class AdminRendezVousController extends Controller
{
    /**
     * Liste des rendez-vous (vue admin)
     */
    public function index(Request $request)
    {
        // ✅ Relations complètes
        $query = RendezVous::with([
            'agence.user',
            'particulier.user',
            'proposition.bien.medias',
            'proposition.demande',
        ]);

        // Filtre par statut
        if ($request->filled('filtre')) {
            $query->where('statut', $request->filtre);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Agence
                $q->whereHas('agence', function ($sub) use ($search) {
                    $sub->where('nom_agence', 'like', "%{$search}%")
                        ->orWhere('adresse', 'like', "%{$search}%");
                })
                // Particulier
                ->orWhereHas('particulier.user', function ($sub) use ($search) {
                    $sub->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                // Bien
                ->orWhereHas('proposition.bien', function ($sub) use ($search) {
                    $sub->where('titre', 'like', "%{$search}%")
                        ->orWhere('adresse', 'like', "%{$search}%");
                })
                // Code de visite
                ->orWhere('code_visite', 'like', "%{$search}%");
            });
        }

        $rendezVous = $query->orderBy('date_visite', 'desc')->paginate(20);

        // ✅ Stats complètes (avec client_absent)
        $stats = [
            'total'         => RendezVous::count(),
            'planifies'     => RendezVous::where('statut', StatutRendezVousEnum::PLANIFIE->value)->count(),
            'confirmes'     => RendezVous::where('statut', StatutRendezVousEnum::CONFIRME->value)->count(),
            'termines'      => RendezVous::where('statut', StatutRendezVousEnum::TERMINE->value)->count(),
            'annules'       => RendezVous::where('statut', StatutRendezVousEnum::ANNULE->value)->count(),
            'client_absent' => RendezVous::where('statut', StatutRendezVousEnum::CLIENT_ABSENT->value)->count(),
        ];

        return view('admin.rendezvous.index', compact('rendezVous', 'stats'));
    }

    /**
     * Détail d'un rendez-vous (vue admin)
     */
    public function show(RendezVous $rendezVous)
    {
        $rendezVous->load([
            'agence.user',
            'particulier.user',
            'proposition.bien.medias',
            'proposition.demande',
        ]);

        // Charger les créneaux de l'agence séparément si la relation existe
        if ($rendezVous->agence && method_exists($rendezVous->agence, 'creneaux')) {
            $rendezVous->agence->load('creneaux');
        }

        return view('admin.rendezvous.show', compact('rendezVous'));
    }
}