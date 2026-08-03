<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    // ✅ Optionnel : relation avec le bien via la proposition
    public function bien()
    {
        return $this->hasOneThrough(BienImmobilier::class, Proposition::class, 'id', 'id', 'proposition_id', 'bien_id');
    }
}