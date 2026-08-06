<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Agence;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Http\Request;

class AdminAbonnementController extends Controller
{
    /**
     * Liste des abonnements
     */
    public function index()
    {
        $abonnements = Abonnement::with('agence.user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Abonnement::count(),
            'actifs' => Abonnement::where('statut', true)->where('date_fin', '>', now())->count(),
            'expires' => Abonnement::where('statut', false)->orWhere('date_fin', '<=', now())->count(),
            'par_formule' => Abonnement::selectRaw('formule, count(*) as total')
                ->groupBy('formule')
                ->get(),
        ];

        return view('admin.abonnements.index', compact('abonnements', 'stats'));
    }

    /**
     * Détail d'un abonnement
     */
    public function show(Abonnement $abonnement)
    {
        $abonnement->load('agence.user');
        return view('admin.abonnements.show', compact('abonnement'));
    }

    /**
     * SUPPRIMÉ : create() - Les agences souscrivent elles-mêmes
     * SUPPRIMÉ : store() - Les agences souscrivent elles-mêmes
     * SUPPRIMÉ : edit() - L'admin ne modifie pas les caractéristiques
     * SUPPRIMÉ : update() - L'admin ne modifie pas les caractéristiques
     */

    /**
     * Activer un abonnement (réactiver après suspension)
     */
    public function activer(Abonnement $abonnement)
    {
        $abonnement->update(['statut' => true]);
        
        // Désactiver les autres abonnements de l'agence
        Abonnement::where('agence_id', $abonnement->agence_id)
            ->where('id', '!=', $abonnement->id)
            ->where('statut', true)
            ->update(['statut' => false]);

        return redirect()->route('admin.abonnements.index')
            ->with('success', 'Abonnement réactivé avec succès.');
    }

    /**
     * Désactiver un abonnement (suspendre)
     */
    public function desactiver(Abonnement $abonnement)
    {
        $abonnement->update(['statut' => false]);

        return redirect()->route('admin.abonnements.index')
            ->with('success', 'Abonnement suspendu avec succès.');
    }

    /**
     * Supprimer un abonnement
     */
    public function destroy(Abonnement $abonnement)
    {
        $abonnement->delete();
        return redirect()->route('admin.abonnements.index')
            ->with('success', 'Abonnement supprimé avec succès.');
    }
}