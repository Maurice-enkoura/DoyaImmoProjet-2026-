<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatutPropositionEnum;

class Evaluation extends Model
{
    use HasFactory;

    protected $table = 'evaluations';

    protected $fillable = [
        'particulier_id',
        'agence_id',
        'note',
        'proposition_id',
        'commentaire',
        'date_evaluation',
        'reponse_agence',
        'date_reponse',
    ];

    protected $casts = [
        'note' => 'integer',
        'date_evaluation' => 'datetime',
        'date_reponse' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function proposition(): BelongsTo
    {
        return $this->belongsTo(Proposition::class);
    }

    // ==================== SCOPES ====================

    public function scopePourAgence($query, $agenceId)
    {
        return $query->where('agence_id', $agenceId);
    }

    public function scopePourParticulier($query, $particulierId)
    {
        return $query->where('particulier_id', $particulierId);
    }

    public function scopePourProposition($query, $propositionId)
    {
        return $query->where('proposition_id', $propositionId);
    }

    // ==================== VALIDATION ====================

    /**
     * Vérifier si un particulier peut évaluer une proposition
     */
    public static function peutEvaluerProposition($particulierId, $propositionId): bool
    {
        $proposition = Proposition::find($propositionId);
        if (!$proposition) {
            return false;
        }

        // ✅ Récupérer la valeur du statut (gère les Enums et les strings)
        $statutValue = $proposition->statut instanceof \UnitEnum ? $proposition->statut->value : $proposition->statut;

        // ✅ Vérifier que le statut est terminé
        if ($statutValue !== 'terminee') {
            return false;
        }

        // ✅ Vérifier que le particulier est bien le destinataire
        if ($proposition->particulier_id !== $particulierId) {
            return false;
        }

        // ✅ Vérifier qu'il n'y a pas déjà une évaluation
        return !self::where('particulier_id', $particulierId)
            ->where('proposition_id', $propositionId)
            ->exists();
    }

    /**
     * Vérifier si un particulier a déjà évalué une proposition
     */
    public static function aDejaEvalueProposition($particulierId, $propositionId): bool
    {
        return self::where('particulier_id', $particulierId)
            ->where('proposition_id', $propositionId)
            ->exists();
    }
}