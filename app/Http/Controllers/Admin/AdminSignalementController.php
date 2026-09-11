<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Signalement;
use App\Enums\StatutSignalementEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSignalementController extends Controller
{
    public function index(Request $request)
    {
        $query = Signalement::with(['agence.user', 'particulier.user', 'signalable']);

        // Filtre par statut
        if ($request->filled('filtre')) {
            switch ($request->filtre) {
                case 'en_attente':
                    $query->where('statut', StatutSignalementEnum::EN_ATTENTE);
                    break;
                case 'traites':
                    $query->where('statut', StatutSignalementEnum::TRAITE);
                    break;
                case 'rejetes':
                    $query->where('statut', StatutSignalementEnum::REJETE);
                    break;
            }
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('motif', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('agence.user', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('particulier.user', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $signalements = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Signalement::count(),
            'en_attente' => Signalement::where('statut', StatutSignalementEnum::EN_ATTENTE)->count(),
            'traites' => Signalement::where('statut', StatutSignalementEnum::TRAITE)->count(),
            'rejetes' => Signalement::where('statut', StatutSignalementEnum::REJETE)->count(),
        ];

        return view('admin.signalements.index', compact('signalements', 'stats'));
    }

    public function show(Signalement $signalement)
    {
        $signalement->load([
            'agence.user',
            'agence.biens',
            'agence.documents',
            'particulier.user',
            'particulier.demandes',
            'signalable'
        ]);

        return view('admin.signalements.show', compact('signalement'));
    }

    public function traiter(Request $request, Signalement $signalement)
    {
        $request->validate([
            'action' => 'required|in:bloquer,rejeter',
            'motif' => 'required_if:action,rejeter|string|max:1000',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            if ($request->action === 'bloquer') {
                $agence = $signalement->agence;
                if ($agence) {
                    $agence->update(['bloque' => true]);
                }

                $signalement->update([
                    'statut' => StatutSignalementEnum::TRAITE,
                    'date_traitement' => now(),
                    'commentaire_admin' => $request->commentaire ?? 'Agence bloquée suite à signalement',
                ]);

                $message = 'Agence bloquée avec succès suite au signalement.';
            } else {
                $signalement->update([
                    'statut' => StatutSignalementEnum::REJETE,
                    'date_traitement' => now(),
                    'commentaire_admin' => $request->motif,
                ]);

                $message = 'Signalement rejeté. Motif enregistré.';
            }

            DB::commit();

            return redirect()->route('admin.signalements.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.signalements.index')
                ->with('error', 'Erreur lors du traitement: ' . $e->getMessage());
        }
    }

    public function rejeter(Signalement $signalement)
    {
        $signalement->update([
            'statut' => StatutSignalementEnum::REJETE,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.signalements.index')
            ->with('success', 'Signalement rejeté.');
    }

    public function sanctionner(Request $request, Signalement $signalement)
    {
        $request->validate([
            'commentaire' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $signalement->update([
                'statut' => StatutSignalementEnum::TRAITE,
                'date_traitement' => now(),
                'commentaire_admin' => $request->commentaire,
            ]);

            $agence = $signalement->agence;
            if ($agence) {
                $agence->update(['bloque' => true]);
            }

            DB::commit();

            return redirect()->route('admin.signalements.index')
                ->with('success', 'Signalement traité et agence bloquée.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.signalements.index')
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}