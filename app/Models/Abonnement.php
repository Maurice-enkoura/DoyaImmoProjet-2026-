<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FormuleAbonnementEnum;
use App\Services\PayDunyaService;

class Abonnement extends Model
{
    use HasFactory;

    protected $table = 'abonnements';

    protected $fillable = [
        'agence_id',
        'formule',
        'montant',
        'date_debut',
        'date_fin',
        'statut',
        'paydunya_token',
        'paydunya_status',
        'paydunya_paid_at',
    ];

    protected $casts = [
        'formule' => FormuleAbonnementEnum::class,
        'montant' => 'decimal:2',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'statut' => 'boolean',
        'paydunya_paid_at' => 'datetime',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', true)
            ->where('date_fin', '>', now());
    }

    public function scopeExpirant($query, int $days = 7)
    {
        return $query->where('statut', true)
            ->whereBetween('date_fin', [now(), now()->addDays($days)]);
    }

    public function estActif(): bool
    {
        return $this->statut && $this->date_fin > now();
    }

    public function estExpirant(int $days = 7): bool
    {
        return $this->statut && $this->date_fin->between(now(), now()->addDays($days));
    }

    /**
     * Génère l'URL de paiement PayDunya pour cet abonnement
     */
    public function getPayDunyaUrl(): string
    {
        try {
            $paydunya = app(PayDunyaService::class);
            $result = $paydunya->createInvoice($this, $this->agence);
            
            if ($result['success']) {
                return $result['invoice_url'];
            }
            
            return '';
        } catch (\Exception $e) {
            \Log::error('Erreur PayDunya: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Vérifie si l'abonnement a un token PayDunya
     */
    public function hasPayDunyaToken(): bool
    {
        return !empty($this->paydunya_token);
    }

    /**
     * Vérifie si le paiement PayDunya est en attente
     */
    public function isPayDunyaPending(): bool
    {
        return $this->paydunya_status === 'pending' && !$this->estActif();
    }

    /**
     * Vérifie si le paiement PayDunya a été payé
     */
    public function isPayDunyaPaid(): bool
    {
        return $this->paydunya_status === 'paid' || $this->estActif();
    }

    // Dans app/Models/Abonnement.php


}