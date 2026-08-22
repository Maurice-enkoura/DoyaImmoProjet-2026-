<?php
// app/Services/PayDunyaService.php

namespace App\Services;

use Paydunya\Setup;
use Paydunya\Checkout\CheckoutInvoice;
use Paydunya\Checkout\Store;
use App\Models\Abonnement;
use App\Models\Agence;
use Illuminate\Support\Facades\Log;

class PayDunyaService
{
    protected $store;
    protected $invoice;

    public function __construct()
    {
        try {
            // Configuration PayDunya - Version 1.0.7
            // Utiliser les bonnes constantes
            Setup::setMasterKey(config('paydunya.master_key'));
            Setup::setPrivateKey(config('paydunya.private_key'));
            Setup::setPublicKey(config('paydunya.public_key'));
            Setup::setToken(config('paydunya.token'));
            Setup::setMode(config('paydunya.mode', 'test'));

            $this->store = new Store();
            $this->store->setName(config('paydunya.store.name', 'DoyaImmo'));
            $this->store->setWebsiteUrl(config('paydunya.store.url', 'http://localhost:8000'));
            
            if (config('paydunya.store.logo')) {
                $this->store->setLogoUrl(config('paydunya.store.logo'));
            }

            $this->invoice = new CheckoutInvoice($this->store);
            
            Log::info('PayDunya Service initialisé avec succès');
            
        } catch (\Exception $e) {
            Log::error('Erreur configuration PayDunya: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Créer une facture pour un abonnement
     */
    /**
 * Créer une facture pour un abonnement
 */
public function createInvoice(Abonnement $abonnement, Agence $agence): array
{
    try {
        $formuleLabel = is_object($abonnement->formule) && method_exists($abonnement->formule, 'label') 
            ? $abonnement->formule->label() 
            : ucfirst($abonnement->formule);

        $montant = floatval($abonnement->montant);

        // Ajouter l'article
        $this->invoice->addItem(
            'Abonnement ' . $formuleLabel,
            1,
            $montant,
            $montant,
            'Abonnement pour l\'agence ' . $agence->nom_agence
        );

        // Configuration de la facture
        $this->invoice->setTotalAmount($montant);
        $this->invoice->setDescription('Abonnement ' . $formuleLabel . ' - ' . $agence->nom_agence);
        $this->invoice->setCallbackUrl(route('paydunya.callback'));
        $this->invoice->setReturnUrl(route('paydunya.return'));  // ✅ CORRECT
        $this->invoice->setCancelUrl(route('paydunya.cancel'));

        // Données personnalisées
        $this->invoice->addCustomData('abonnement_id', $abonnement->id);
        $this->invoice->addCustomData('agence_id', $agence->id);

        // Créer la facture
        $this->invoice->create();

        Log::info('PayDunya createInvoice - Réponse', [
            'response_code' => $this->invoice->response_code,
            'response_text' => $this->invoice->response_text,
            'token' => $this->invoice->token,
        ]);

        // Vérifier le résultat
        if ($this->invoice->response_code === '00') {
            $token = $this->invoice->token;
            
            $abonnement->update([
                'paydunya_token' => $token,
                'paydunya_status' => 'pending',
            ]);

            return [
                'success' => true,
                'invoice_url' => $this->invoice->getInvoiceUrl(),
                'token' => $token,
                'response_text' => $this->invoice->response_text,
            ];
        }

        // Erreur
        $errorMessage = $this->invoice->response_text ?? 'Erreur de création';
        Log::error('PayDunya createInvoice - Erreur', [
            'response_code' => $this->invoice->response_code,
            'response_text' => $errorMessage,
        ]);

        return [
            'success' => false,
            'error' => $errorMessage,
            'response_code' => $this->invoice->response_code,
        ];

    } catch (\Exception $e) {
        Log::error('PayDunya createInvoice error: ' . $e->getMessage());
        Log::error($e->getTraceAsString());
        return [
            'success' => false,
            'error' => $e->getMessage(),
        ];
    }
}

    /**
     * Confirmer le paiement
     */
    public function confirmInvoice(string $token): array
    {
        try {
            $this->invoice = new CheckoutInvoice($this->store);
            $this->invoice->token = $token;
            $this->invoice->confirm();

            $isPaid = $this->invoice->getStatus() === 'completed' || $this->invoice->response_code === '00';

            Log::info('PayDunya confirmInvoice', [
                'token' => $token,
                'status' => $this->invoice->getStatus(),
                'is_paid' => $isPaid,
                'response_text' => $this->invoice->response_text,
            ]);

            return [
                'success' => $isPaid,
                'status' => $this->invoice->getStatus(),
                'is_paid' => $isPaid,
                'response_text' => $this->invoice->response_text,
            ];

        } catch (\Exception $e) {
            Log::error('PayDunya confirmInvoice error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Vérifier le statut d'une facture
     */
    public function getInvoiceStatus(string $token): array
    {
        try {
            $this->invoice = new CheckoutInvoice($this->store);
            $this->invoice->token = $token;
            $this->invoice->getStatus();

            return [
                'success' => true,
                'status' => $this->invoice->getStatus(),
                'is_paid' => $this->invoice->getStatus() === 'completed',
                'response_text' => $this->invoice->response_text,
            ];

        } catch (\Exception $e) {
            Log::error('PayDunya getInvoiceStatus error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}