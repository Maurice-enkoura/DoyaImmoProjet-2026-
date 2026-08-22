<?php
// app/Http/Controllers/PayDunyaController.php

namespace App\Http\Controllers;

use App\Models\Abonnement;
use App\Services\PayDunyaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayDunyaController extends Controller
{
    protected $paydunya;

    public function __construct(PayDunyaService $paydunya)
    {
        $this->paydunya = $paydunya;
    }

    /**
     * Payer un abonnement
     * Route: GET /paydunya/pay/{abonnement}
     */
    public function pay(Abonnement $abonnement)
    {
        try {
            Log::info('PayDunya pay - Début pour abonnement #' . $abonnement->id);

            if (!$abonnement) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Abonnement non trouvé.');
            }

            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('error', 'Veuillez vous connecter.');
            }

            $agence = auth()->user()->agence;
            if (!$agence || $abonnement->agence_id != $agence->id) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Cet abonnement ne vous appartient pas.');
            }

            if (floatval($abonnement->montant) <= 0) {
                $abonnement->update([
                    'statut' => true,
                    'paydunya_status' => 'free',
                    'paydunya_paid_at' => now(),
                ]);

                if ($abonnement->agence) {
                    Abonnement::where('agence_id', $abonnement->agence_id)
                        ->where('id', '!=', $abonnement->id)
                        ->where('statut', true)
                        ->update(['statut' => false]);
                }

                return redirect()->route('agence.abonnement')
                    ->with('success', '✅ Abonnement gratuit activé avec succès !');
            }

            if ($abonnement->estActif()) {
                return redirect()->route('agence.abonnement')
                    ->with('info', 'Cet abonnement est déjà actif.');
            }

            if ($abonnement->paydunya_token && $abonnement->paydunya_status === 'pending') {
                $statusResult = $this->paydunya->getInvoiceStatus($abonnement->paydunya_token);
                
                if ($statusResult['status'] === 'completed') {
                    $abonnement->update([
                        'statut' => true,
                        'paydunya_status' => 'paid',
                        'paydunya_paid_at' => now(),
                    ]);

                    if ($abonnement->agence) {
                        Abonnement::where('agence_id', $abonnement->agence_id)
                            ->where('id', '!=', $abonnement->id)
                            ->where('statut', true)
                            ->update(['statut' => false]);
                    }

                    return redirect()->route('agence.abonnement')
                        ->with('success', '✅ Paiement confirmé avec succès !');
                }

                if ($statusResult['status'] === 'expired' || $statusResult['status'] === 'cancelled') {
                    $abonnement->update([
                        'paydunya_token' => null,
                        'paydunya_status' => null,
                    ]);
                } else {
                    return redirect()->route('paydunya.status', $abonnement);
                }
            }

            $result = $this->paydunya->createInvoice($abonnement, $agence);

            if ($result['success'] && isset($result['invoice_url'])) {
                return redirect()->away($result['invoice_url']);
            }

            $error = $result['error'] ?? 'Erreur lors de la création du paiement.';
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur: ' . $error);

        } catch (\Exception $e) {
            Log::error('PayDunya pay error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Callback de PayDunya (après paiement)
     * Route: GET|POST /paydunya/callback
     */
    public function callback(Request $request)
    {
        try {
            $token = $request->input('token');
            $status = $request->input('status');
            $transactionId = $request->input('transaction_id');
            
            Log::info('PayDunya callback - Données reçues', [
                'token' => $token,
                'status' => $status,
                'transaction_id' => $transactionId,
                'all' => $request->all(),
                'method' => $request->method()
            ]);

            if (!$token) {
                Log::error('PayDunya callback: Token manquant');
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Token de paiement manquant.');
            }

            $abonnement = Abonnement::where('paydunya_token', $token)->first();
            
            if (!$abonnement) {
                Log::error('PayDunya callback: Abonnement non trouvé pour le token: ' . $token);
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Abonnement non trouvé.');
            }

            if ($abonnement->estActif()) {
                return redirect()->route('agence.abonnement')
                    ->with('info', 'Cet abonnement est déjà actif.');
            }

            $result = $this->paydunya->confirmInvoice($token);

            Log::info('PayDunya callback - Résultat confirmation', [
                'result' => $result,
                'abonnement_id' => $abonnement->id
            ]);

            if ($result['success'] || $result['is_paid'] || $status === 'completed' || $status === 'paid') {
                $abonnement->update([
                    'statut' => true,
                    'paydunya_status' => 'paid',
                    'paydunya_paid_at' => now(),
                ]);

                if ($abonnement->agence) {
                    Abonnement::where('agence_id', $abonnement->agence_id)
                        ->where('id', '!=', $abonnement->id)
                        ->where('statut', true)
                        ->update(['statut' => false]);
                }

                Log::info('PayDunya callback: ✅ Paiement confirmé pour l\'abonnement #' . $abonnement->id);
                
                return redirect()->route('agence.abonnement')
                    ->with('success', '✅ Paiement confirmé avec succès ! Votre abonnement est maintenant actif.');
            }

            if ($result['status'] === 'pending') {
                return redirect()->route('agence.abonnement')
                    ->with('info', '⏳ Votre paiement est en cours de traitement. Veuillez patienter.');
            }

            Log::error('PayDunya callback: ❌ Échec du paiement', ['result' => $result]);
            return redirect()->route('agence.abonnement')
                ->with('error', '❌ Erreur lors de la confirmation du paiement. Veuillez contacter le support.');

        } catch (\Exception $e) {
            Log::error('PayDunya callback error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors du traitement du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Forcer la mise à jour d'un abonnement (route de secours)
     * Route: GET /paydunya/force-update/{abonnement}
     */
    public function forceUpdate(Abonnement $abonnement)
    {
        try {
            if (!$abonnement) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Abonnement non trouvé.');
            }

            $agence = auth()->user()->agence;
            if (!$agence || $abonnement->agence_id != $agence->id) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Action non autorisée.');
            }

            $abonnement->update([
                'statut' => true,
                'paydunya_status' => 'paid',
                'paydunya_paid_at' => now(),
            ]);

            if ($abonnement->agence) {
                Abonnement::where('agence_id', $abonnement->agence_id)
                    ->where('id', '!=', $abonnement->id)
                    ->where('statut', true)
                    ->update(['statut' => false]);
            }

            return redirect()->route('agence.abonnement')
                ->with('success', '✅ Abonnement activé manuellement avec succès !');

        } catch (\Exception $e) {
            Log::error('PayDunya forceUpdate error: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Annulation du paiement
     * Route: GET /paydunya/cancel
     */
    public function cancel(Request $request)
    {
        try {
            $token = $request->input('token');
            
            if ($token) {
                $abonnement = Abonnement::where('paydunya_token', $token)->first();
                if ($abonnement) {
                    $abonnement->update([
                        'paydunya_status' => 'cancelled',
                        'paydunya_token' => null,
                    ]);
                    Log::info('PayDunya: Paiement annulé pour l\'abonnement #' . $abonnement->id);
                    
                    return redirect()->route('agence.abonnement')
                        ->with('info', 'Le paiement a été annulé.');
                }
            }

            return redirect()->route('agence.abonnement')
                ->with('info', 'Le paiement a été annulé.');

        } catch (\Exception $e) {
            Log::error('PayDunya cancel error: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de l\'annulation.');
        }
    }

    /**
     * Vérifier le statut d'un paiement
     * Route: GET /paydunya/status/{abonnement}
     */
    public function status(Abonnement $abonnement)
    {
        try {
            if (!$abonnement) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Abonnement non trouvé.');
            }

            if (!$abonnement->paydunya_token) {
                return redirect()->route('agence.abonnement')
                    ->with('info', 'Aucun paiement en cours pour cet abonnement.');
            }

            if ($abonnement->estActif()) {
                return redirect()->route('agence.abonnement')
                    ->with('info', 'Cet abonnement est déjà actif.');
            }

            $result = $this->paydunya->getInvoiceStatus($abonnement->paydunya_token);

            if ($result['is_paid'] || $result['status'] === 'completed') {
                $abonnement->update([
                    'statut' => true,
                    'paydunya_status' => 'paid',
                    'paydunya_paid_at' => now(),
                ]);

                if ($abonnement->agence) {
                    Abonnement::where('agence_id', $abonnement->agence_id)
                        ->where('id', '!=', $abonnement->id)
                        ->where('statut', true)
                        ->update(['statut' => false]);
                }

                return redirect()->route('agence.abonnement')
                    ->with('success', '✅ Le paiement a été confirmé !');
            }

            if ($result['status'] === 'cancelled' || $result['status'] === 'expired') {
                $abonnement->update([
                    'paydunya_status' => $result['status'],
                    'paydunya_token' => null,
                ]);
                return redirect()->route('agence.abonnement')
                    ->with('info', 'Le paiement a été ' . $result['status']);
            }

            return redirect()->route('agence.abonnement')
                ->with('info', 'Statut du paiement: ' . ($result['status'] ?? 'En attente'));

        } catch (\Exception $e) {
            Log::error('PayDunya status error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de la vérification du statut.');
        }
    }



    // Dans app/Http/Controllers/PayDunyaController.php

/**
 * Retour après paiement (page de confirmation)
 * Route: GET /paydunya/return
 */
public function return(Request $request)
{
    try {
        $token = $request->input('token');
        
        Log::info(' PayDunya RETURN appelé', [
            'token' => $token,
            'all' => $request->all()
        ]);

        if (!$token) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Token de paiement manquant.');
        }

        // Récupérer l'abonnement
        $abonnement = Abonnement::where('paydunya_token', $token)->first();
        
        if (!$abonnement) {
            Log::error(' Abonnement non trouvé pour le token: ' . $token);
            return redirect()->route('agence.abonnement')
                ->with('error', 'Abonnement non trouvé.');
        }

        // Vérifier si déjà actif
        if ($abonnement->estActif()) {
            return redirect()->route('agence.abonnement')
                ->with('info', 'Cet abonnement est déjà actif.');
        }

        // 🔥 ACTIVATION AUTOMATIQUE
        $abonnement->statut = true;
        $abonnement->paydunya_status = 'paid';
        $abonnement->paydunya_paid_at = now();
        $abonnement->save();

        // Désactiver les autres abonnements de l'agence
        Abonnement::where('agence_id', $abonnement->agence_id)
            ->where('id', '!=', $abonnement->id)
            ->where('statut', true)
            ->update(['statut' => false]);

        Log::info('Abonnement #' . $abonnement->id . ' activé avec succès via return');

        return redirect()->route('agence.abonnement')
            ->with('success', ' Paiement confirmé avec succès ! Votre abonnement est maintenant actif.');

    } catch (\Exception $e) {
        Log::error(' PayDunya return error: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return redirect()->route('agence.abonnement')
            ->with('error', 'Erreur lors du traitement du paiement: ' . $e->getMessage());
    }
}
}