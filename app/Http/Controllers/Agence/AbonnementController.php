<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Agence;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbonnementController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        // Vérifier si l'abonnement gratuit est expiré
        $abonnementGratuitExpire = false;
        $dernierAbonnement = $agence->abonnements()
            ->where('formule', 'basic')
            ->where('statut', false)
            ->orderBy('date_fin', 'desc')
            ->first();

        if ($dernierAbonnement && $dernierAbonnement->date_fin < now()) {
            $abonnementGratuitExpire = true;
        }

        $historique = $agence->abonnements()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Plans disponibles
        $plans = [
            'basic' => [
                'label' => 'Basique',
                'price' => 0,
                'price_label' => 'Gratuit',
                'features' => [
                    '5 offres envoyées / mois',
                    'Accès aux besoins publics',
                    'Profil agence simple'
                ],
                'limite' => 5,
                'badge' => null
            ],
            'standard' => [
                'label' => 'Standard',
                'price' => 10000,
                'price_label' => '10 000 F',
                'features' => [
                    '30 offres envoyées / mois',
                    'Mise en avant des annonces',
                    'Statistiques de performance',
                    'Support prioritaire'
                ],
                'limite' => 30,
                'badge' => null
            ],
            'premium' => [
                'label' => 'Premium',
                'price' => 35000,
                'price_label' => '35 000 F',
                'features' => [
                    'Offres illimitées',
                    'Badge "Agence Premium"',
                    'Accès anticipé aux nouveaux besoins',
                    'Statistiques avancées',
                    'Gestionnaire de compte dédié'
                ],
                'limite' => PHP_INT_MAX,
                'badge' => 'Recommandé'
            ]
        ];

        return view('agence.abonnement.index', compact(
            'abonnementActuel',
            'historique',
            'plans',
            'abonnementGratuitExpire'
        ));
    }

    public function souscrire(Request $request)
    {
        $request->validate([
            'formule' => 'required|in:basic,standard,premium'
        ]);

        $agence = Auth::user()->agence;
        $formule = FormuleAbonnementEnum::from($request->formule);

        // Désactiver l'ancien abonnement
        Abonnement::where('agence_id', $agence->id)
            ->where('statut', true)
            ->update(['statut' => false]);

        // Durée de l'abonnement
        $duree = $request->formule === 'basic' ? 30 : 30;

        // Créer le nouvel abonnement
        Abonnement::create([
            'agence_id' => $agence->id,
            'formule' => $formule,
            'montant' => $formule->prix(),
            'date_debut' => now(),
            'date_fin' => now()->addDays($duree),
            'statut' => true,
        ]);

        return redirect()->route('agence.abonnement')
            ->with('success', 'Abonnement souscrit avec succès !');
    }

    public function plans()
    {
        $agence = Auth::user()->agence;
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        // Plans disponibles
        $plans = [
            'basic' => [
                'label' => 'Basique',
                'price' => 0,
                'price_label' => 'Gratuit',
                'features' => [
                    '5 offres envoyées / mois',
                    'Accès aux besoins publics',
                    'Profil agence'
                ],
                'limite' => 5,
                'badge' => null
            ],
            'standard' => [
                'label' => 'Standard',
                'price' => 10000,
                'price_label' => '10 000 F',
                'features' => [
                    '30 offres envoyées / mois',
                    'Mise en avant des annonces',
                    'Profil agence',
                    
                ],
                'limite' => 30,
                'badge' => null
            ],
            'premium' => [
                'label' => 'Premium',
                'price' => 15000,
                'price_label' => '15 000 F',
                'features' => [
                    'Offres illimitées',
                    'Badge "Agence Premium"',
                    'Accès anticipé aux nouveaux besoins',
                    'Profil agence',
                    
                    
                ],
                'limite' => PHP_INT_MAX,
                'badge' => 'Recommandé'
            ]
        ];

        return view('agence.abonnement.plans', compact('abonnementActuel', 'plans'));
    }
}