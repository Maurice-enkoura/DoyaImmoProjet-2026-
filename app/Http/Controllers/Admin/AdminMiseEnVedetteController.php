<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MiseEnVedette;
use App\Models\BienImmobilier;
use App\Notifications\MiseEnVedetteValideeNotification;
use App\Notifications\MiseEnVedetteAnnuleeNotification;
use App\Notifications\NouvelleDemandeVedetteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminMiseEnVedetteController extends Controller
{
    /**
     * Liste des demandes de mise en vedette
     */
    public function index(Request $request)
    {
        $query = MiseEnVedette::with(['bien', 'agence.user']);

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche par agence ou bien
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('agence', function ($sub) use ($search) {
                    $sub->where('nom_agence', 'like', "%{$search}%");
                })->orWhereHas('bien', function ($sub) use ($search) {
                    $sub->where('titre', 'like', "%{$search}%")
                        ->orWhere('quartier', 'like', "%{$search}%");
                });
            });
        }

        $mises = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques
        $stats = [
            'en_attente' => MiseEnVedette::where('statut', 'en_attente')->count(),
            'actif' => MiseEnVedette::where('statut', 'actif')->count(),
            'expire' => MiseEnVedette::where('statut', 'expire')->count(),
            'annule' => MiseEnVedette::where('statut', 'annule')->count(),
        ];

        return view('admin.mises-vedette.index', compact('mises', 'stats'));
    }

    /**
     * Détail d'une demande de mise en vedette
     */
    public function show(MiseEnVedette $mise)
    {
        $mise->load(['bien', 'agence.user', 'valideePar']);
        return view('admin.mises-vedette.show', compact('mise'));
    }

    /**
     * Valider une demande de mise en vedette
     */
    public function valider(Request $request, MiseEnVedette $mise)
    {
        try {
            // Vérifier que la demande est en attente
            if ($mise->statut !== 'en_attente') {
                return redirect()->back()
                    ->with('error', 'Cette demande a déjà été traitée.');
            }

            $request->validate([
                'commentaire' => 'nullable|string|max:500',
            ]);

            // Vérifier que le bien existe toujours
            if (!$mise->bien) {
                return redirect()->back()
                    ->with('error', 'Le bien associé n\'existe plus.');
            }

            // Mettre à jour la mise en vedette
            $mise->update([
                'statut' => 'actif',
                'date_debut' => now(),
                'date_fin' => now()->addDays($mise->duree),
                'commentaire_admin' => $request->commentaire,
                'validee_par_admin_at' => now(),
                'validee_par_admin_id' => Auth::id(),
            ]);

            // Mettre à jour le bien
            $bien = $mise->bien;
            $bien->update([
                'est_vedette' => true,
                'vedette_debut' => now(),
                'vedette_fin' => now()->addDays($mise->duree),
            ]);

            // ✅ ENVOYER LA NOTIFICATION À L'AGENCE
            $agence = $mise->agence;
            if ($agence && $agence->user) {
                try {
                    $agence->user->notify(new MiseEnVedetteValideeNotification($mise));
                    Log::info('Notification de validation envoyée à l\'agence ID: ' . $agence->id);
                } catch (\Exception $e) {
                    Log::error('Erreur envoi notification validation: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.mises-vedette.show', $mise)
                ->with('success', "✅ La mise en vedette a été activée pour {$mise->duree} jours.");

        } catch (\Exception $e) {
            Log::error('Erreur validation mise en vedette: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Annuler une demande de mise en vedette
     */
    public function annuler(Request $request, MiseEnVedette $mise)
    {
        try {
            // Vérifier que la demande peut être annulée
            if (!in_array($mise->statut, ['en_attente', 'actif'])) {
                return redirect()->back()
                    ->with('error', 'Cette demande ne peut pas être annulée.');
            }

            $request->validate([
                'commentaire' => 'nullable|string|max:500',
            ]);

            // Sauvegarder l'ancien statut
            $ancienStatut = $mise->statut;

            // Mettre à jour la mise en vedette
            $mise->update([
                'statut' => 'annule',
                'commentaire_admin' => $request->commentaire ?? $mise->commentaire_admin,
            ]);

            // Si la mise était active, désactiver le bien
            if ($ancienStatut === 'actif' && $mise->bien) {
                $bien = $mise->bien;
                $bien->update([
                    'est_vedette' => false,
                    'vedette_debut' => null,
                    'vedette_fin' => null,
                ]);
            }

            // ✅ ENVOYER LA NOTIFICATION À L'AGENCE
            $agence = $mise->agence;
            if ($agence && $agence->user) {
                try {
                    $agence->user->notify(new MiseEnVedetteAnnuleeNotification($mise));
                    Log::info('Notification d\'annulation envoyée à l\'agence ID: ' . $agence->id);
                } catch (\Exception $e) {
                    Log::error('Erreur envoi notification annulation: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.mises-vedette.show', $mise)
                ->with('success', '✅ La demande de mise en vedette a été annulée.');

        } catch (\Exception $e) {
            Log::error('Erreur annulation mise en vedette: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'annulation: ' . $e->getMessage());
        }
    }

    /**
     * Vérifier et désactiver automatiquement les mises en vedette expirées
     * (À appeler via un cron job)
     */
    public function verifierExpirations()
    {
        try {
            // Récupérer les mises en vedette actives expirées
            $misesExpirees = MiseEnVedette::where('statut', 'actif')
                ->where('date_fin', '<', now())
                ->get();

            $count = 0;
            foreach ($misesExpirees as $mise) {
                // Mettre à jour le statut de la mise en vedette
                $mise->update(['statut' => 'expire']);

                // Désactiver la vedette sur le bien
                if ($mise->bien) {
                    $mise->bien->update([
                        'est_vedette' => false,
                        'vedette_debut' => null,
                        'vedette_fin' => null,
                    ]);
                }
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} mise(s) en vedette expirée(s) désactivée(s)."
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur vérification expirations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Statistiques des mises en vedette pour le dashboard
     */
    public function getStats()
    {
        return [
            'en_attente' => MiseEnVedette::where('statut', 'en_attente')->count(),
            'actif' => MiseEnVedette::where('statut', 'actif')->count(),
            'expire' => MiseEnVedette::where('statut', 'expire')->count(),
            'annule' => MiseEnVedette::where('statut', 'annule')->count(),
            'total' => MiseEnVedette::count(),
            'revenus_total' => MiseEnVedette::whereIn('statut', ['actif', 'expire'])->sum('montant'),
            'revenus_mois' => MiseEnVedette::whereIn('statut', ['actif', 'expire'])
                ->whereMonth('created_at', now()->month)
                ->sum('montant'),
        ];
    }
}