<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Agence;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        
        // Calculer la durée en mois
        $debut = Carbon::parse($abonnement->date_debut);
        $fin = Carbon::parse($abonnement->date_fin);
        $dureeMois = $debut->diffInMonths($fin);
        
        // Vérifier si l'abonnement est sur le point d'expirer
        $expireBientot = $abonnement->estActif() && $abonnement->date_fin->diffInDays(now()) <= 7;
        
        return view('admin.abonnements.show', compact('abonnement', 'dureeMois', 'expireBientot'));
    }

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