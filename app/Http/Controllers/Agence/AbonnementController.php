<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Agence;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AbonnementController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;
        
        if (!$agence) {
            return redirect()->route('agence.dashboard')
                ->with('error', 'Agence non trouvée.');
        }

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

        // ✅ Plans disponibles (seulement Basic et Pro)
        $plans = [
            'basic' => [
                'label' => 'Gratuit',
                'price' => 0,
                'price_label' => '0 FCFA/mois',
                'period' => '1 mois',
                'features' => [
                    'Création du compte agence',
                    'Profil agence',
                    'Consultation des demandes',
                    'Accès aux demandes pertinentes',
                    '5 propositions par mois',
                    'Gestion des rendez-vous',
                ],
                'limite' => 5,
                'badge' => null,
                'icon' => 'fa-regular fa-star',
                'color' => '#6A7280'
            ],
            'pro' => [
                'label' => 'Pro',
                'price' => 5000,
                'price_label' => '5 000 FCFA/mois',
                'period' => '1 mois',
                'features' => [
                    '✅ Tout ce qui est inclus dans Gratuit',
                    '✅ Propositions illimitées',
                    '✅ Publication de biens immobiliers',
                    '✅ Gestion du portefeuille de biens',
                    '✅ Accès complet aux demandes pertinentes',
                    '✅ Réception des demandes',
                    '✅ Gestion des rendez-vous',
                    '✅ Profil agence professionnel',
                    '✅ Statistiques de base',
                    '⭐ Possibilité de demander une mise en vedette (payant)',
                ],
                'limite' => PHP_INT_MAX,
                'badge' => 'Recommandé',
                'icon' => 'fa-solid fa-gem',
                'color' => '#D4AF37'
            ]
        ];

        // ✅ Tarifs de mise en vedette
        $tarifsVedette = [
            1 => 1000,
            3 => 1500,
            7 => 3000,
            14 => 5000,
            30 => 8000,
        ];

        return view('agence.abonnement.index', compact(
            'abonnementActuel',
            'historique',
            'plans',
            'abonnementGratuitExpire',
            'tarifsVedette',
            'agence'
        ));
    }

    /**
     * Souscrire à un abonnement
     */
    public function souscrire(Request $request)
    {
        try {
            $request->validate([
                'formule' => 'required|in:basic,pro' // ✅ Seulement basic et pro
            ]);

            $agence = Auth::user()->agence;

            if (!$agence) {
                return redirect()->route('agence.dashboard')
                    ->with('error', 'Agence non trouvée.');
            }

            if (!$agence->statut_validation) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Votre agence doit être validée par un administrateur pour souscrire à un abonnement.');
            }

            $formule = FormuleAbonnementEnum::from($request->formule);
            $montant = $formule->prix();

            // ✅ Vérifier si un abonnement actif existe
            $abonnementActuel = $agence->abonnements()
                ->where('statut', true)
                ->where('date_fin', '>', now())
                ->first();

            if ($abonnementActuel) {
                $formuleActuelle = $abonnementActuel->formule->value;
                $formuleDemandee = $request->formule;

                // Même formule → Bloquer
                if ($formuleActuelle === $formuleDemandee) {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà un abonnement ' . $formule->label() . ' actif jusqu\'au ' . $abonnementActuel->date_fin->format('d/m/Y') . '.');
                }

                // ✅ Basic → peut passer à Pro
                if ($formuleActuelle === 'basic' && $formuleDemandee === 'pro') {
                    $abonnementActuel->update(['statut' => false]);
                }
                // ✅ Pro → bloquer tout changement
                elseif ($formuleActuelle === 'pro') {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà un abonnement Pro actif jusqu\'au ' . $abonnementActuel->date_fin->format('d/m/Y') . '. Vous ne pouvez pas changer avant la fin de votre abonnement.');
                } else {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous ne pouvez pas changer d\'abonnement pour le moment.');
                }
            }

            // ✅ Vérifier si l'agence a déjà eu un abonnement gratuit (Basic)
            if ($montant == 0) {
                $aDejaEuGratuit = $agence->abonnements()
                    ->where('formule', 'basic')
                    ->where('statut', false)
                    ->exists();

                if ($aDejaEuGratuit) {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà utilisé votre abonnement gratuit. Veuillez choisir l\'abonnement Pro.');
                }
            }

            // ✅ Créer l'abonnement (1 mois)
            $abonnement = Abonnement::create([
                'agence_id' => $agence->id,
                'formule' => $formule,
                'montant' => $montant,
                'date_debut' => now(),
                'date_fin' => now()->addMonth(),
                'statut' => $montant == 0,
            ]);

            if ($montant > 0) {
                return redirect()->route('paydunya.pay', ['abonnement' => $abonnement->id]);
            }

            return redirect()->route('agence.abonnement')
                ->with('success', '✅ Abonnement gratuit activé avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur souscription: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de la souscription: ' . $e->getMessage());
        }
    }

    /**
     * Changer d'abonnement (upgrade)
     */
    public function upgrade(Request $request)
    {
        try {
            $request->validate([
                'formule' => 'required|in:basic,pro'
            ]);

            $agence = Auth::user()->agence;

            if (!$agence) {
                return redirect()->route('agence.dashboard')
                    ->with('error', 'Agence non trouvée.');
            }

            $formule = FormuleAbonnementEnum::from($request->formule);
            $montant = $formule->prix();

            // Désactiver l'ancien abonnement
            Abonnement::where('agence_id', $agence->id)
                ->where('statut', true)
                ->update(['statut' => false]);

            // Créer le nouvel abonnement
            $abonnement = Abonnement::create([
                'agence_id' => $agence->id,
                'formule' => $formule,
                'montant' => $montant,
                'date_debut' => now(),
                'date_fin' => now()->addMonth(),
                'statut' => $montant == 0,
            ]);

            if ($montant > 0) {
                return redirect()->route('paydunya.pay', ['abonnement' => $abonnement->id]);
            }

            return redirect()->route('agence.abonnement')
                ->with('success', '✅ Abonnement mis à jour avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour abonnement: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Annuler un abonnement
     */
    public function annuler(Abonnement $abonnement)
    {
        try {
            $agence = Auth::user()->agence;

            if ($abonnement->agence_id != $agence->id) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Action non autorisée.');
            }

            $abonnement->update([
                'statut' => false,
                'paydunya_status' => 'cancelled',
            ]);

            return redirect()->route('agence.abonnement')
                ->with('success', '✅ Abonnement annulé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur annulation: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de l\'annulation.');
        }
    }

    /**
     * Liste des plans (API ou page séparée)
     */
    public function plans()
    {
        $agence = Auth::user()->agence;
        
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        // ✅ Plans disponibles (seulement Basic et Pro)
        $plans = [
            'basic' => [
                'label' => 'Gratuit',
                'price' => 0,
                'price_label' => '0 FCFA/mois',
                'period' => '1 mois',
                'features' => [
                    'Création du compte agence',
                    'Profil agence',
                    'Consultation des demandes',
                    'Accès aux demandes pertinentes',
                    '5 propositions par mois',
                    'Gestion des rendez-vous',
                ],
                'limite' => 5,
                'badge' => null,
                'icon' => 'fa-regular fa-star',
                'color' => '#6A7280'
            ],
            'pro' => [
                'label' => 'Pro',
                'price' => 5000,
                'price_label' => '5 000 FCFA/mois',
                'period' => '1 mois',
                'features' => [
                    ' Tout ce qui est inclus dans Gratuit',
                    ' Propositions illimitées',
                    ' Publication de biens immobiliers',
                    ' Gestion du portefeuille de biens',
                    ' Accès complet aux demandes pertinentes',
                    ' Réception des demandes',
                    ' Gestion des rendez-vous',
                    ' Profil agence professionnel',
                    ' Statistiques de base',
                    ' Possibilité de demander une mise en vedette (payant)',
                ],
                'limite' => PHP_INT_MAX,
                'badge' => 'Recommandé',
                'icon' => 'fa-solid fa-gem',
                'color' => '#D4AF37'
            ]
        ];

        return view('agence.abonnement.plans', compact('abonnementActuel', 'plans'));
    }
}