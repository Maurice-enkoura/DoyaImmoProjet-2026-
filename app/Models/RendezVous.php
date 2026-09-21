<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatutRendezVousEnum;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'proposition_id',
        'creneau_id',
        'particulier_id',
        'agence_id',
        'date_visite',
        'heure_visite',
        'statut',
        'code_visite',       // ✅ NOUVEAU
        'code_valide',       // ✅ NOUVEAU
        'code_valide_le',    // ✅ NOUVEAU
    ];

    protected $casts = [
        'statut' => StatutRendezVousEnum::class,
        'date_visite' => 'date',
        'code_valide' => 'boolean',                      // ✅ NOUVEAU
        'code_valide_le' => 'datetime',                  // ✅ NOUVEAU
    ];

    public function proposition(): BelongsTo
    {
        return $this->belongsTo(Proposition::class);
    }

    public function creneau(): BelongsTo
    {
        return $this->belongsTo(CreneauRendezVous::class);
    }

    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function scopePlanifies($query)
    {
        return $query->where('statut', StatutRendezVousEnum::PLANIFIE);
    }

    public function scopeConfirmes($query)
    {
        return $query->where('statut', StatutRendezVousEnum::CONFIRME);
    }

    // ==================== CODE DE VISITE ====================

    /**
     * Génère un code unique à 6 chiffres
     */
    public static function genererCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('code_visite', $code)->where('code_valide', false)->exists());

        return $code;
    }

    /**
     * Génère et assigne le code de visite au RDV
     */
    public function genererCodeVisite(): void
    {
        $this->update([
            'code_visite' => self::genererCode(),
        ]);
    }

    /**
     * Vérifie si le code fourni correspond au code du RDV
     */
    public function verifierCode(string $code): bool
    {
        return $this->code_visite === $code;
    }

    /**
     * Valide la visite avec le code fourni
     */
    public function validerVisite(string $code): bool
    {
        if (!$this->verifierCode($code)) {
            return false;
        }

        $this->update([
            'code_valide'    => true,
            'code_valide_le' => now(),
            'statut'         => StatutRendezVousEnum::TERMINE,
        ]);

        return true;
    }

    // ==================== VISIBILITÉ TÉLÉPHONE ====================

    /**
     * Vérifie si les numéros de téléphone sont visibles
     * Les numéros sont visibles uniquement lorsque le rendez-vous est confirmé
     */
    public function isPhoneVisible(): bool
    {
        return $this->statut === StatutRendezVousEnum::CONFIRME;
    }

    public function getAgencePhoneAttribute(): ?string
    {
        if (!$this->isPhoneVisible()) {
            return null;
        }

        return $this->agence?->telephone;
    }

    public function getParticulierPhoneAttribute(): ?string
    {
        if (!$this->isPhoneVisible()) {
            return null;
        }

        return $this->particulier?->user?->telephone;
    }

    public function getAgencePhoneFormattedAttribute(): string
    {
        if (!$this->isPhoneVisible()) {
            return 'Non disponible';
        }

        $phone = $this->agence?->telephone;
        return $phone ? $this->formatPhone($phone) : 'Non renseigné';
    }

    public function getParticulierPhoneFormattedAttribute(): string
    {
        if (!$this->isPhoneVisible()) {
            return 'Non disponible';
        }

        $phone = $this->particulier?->user?->telephone;
        return $phone ? $this->formatPhone($phone) : 'Non renseigné';
    }

    /**
     * Formate un numéro de téléphone
     */
    private function formatPhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($clean) === 12 && substr($clean, 0, 3) === '221') {
            return '+221 ' . substr($clean, 3, 2) . ' ' . substr($clean, 5, 3) . ' ' . substr($clean, 8, 2) . ' ' . substr($clean, 10, 2);
        }

        if (strlen($clean) === 9) {
            return substr($clean, 0, 2) . ' ' . substr($clean, 2, 3) . ' ' . substr($clean, 5, 2) . ' ' . substr($clean, 7, 2);
        }

        return $phone;
    }

    public function getStatutLabelAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'label')) {
            return $this->statut->label();
        }

        $labels = [
            'planifie' => 'Planifié',
            'confirme' => 'Confirmé',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
        ];

        return $labels[$this->statut] ?? $this->statut;
    }
}