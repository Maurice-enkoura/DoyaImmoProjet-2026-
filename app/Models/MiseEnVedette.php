<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MiseEnVedette extends Model
{
    use HasFactory;

    protected $table = 'mises_en_vedette';

    protected $fillable = [
        'bien_id',
        'agence_id',
        'duree',
        'montant',
        'date_debut',
        'date_fin',
        'statut',
        'commentaire_admin',
        'validee_par_admin_at',
        'validee_par_admin_id',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'validee_par_admin_at' => 'datetime',
    ];

    public function bien()
    {
        return $this->belongsTo(BienImmobilier::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function valideePar()
    {
        return $this->belongsTo(User::class, 'validee_par_admin_id');
    }

    public function estActive(): bool
    {
        return $this->statut === 'actif' && $this->date_fin && $this->date_fin > now();
    }

    public function estExpiree(): bool
    {
        return $this->statut === 'actif' && $this->date_fin && $this->date_fin <= now();
    }

    public function getJoursRestants(): int
    {
        if (!$this->date_fin || $this->date_fin <= now()) {
            return 0;
        }
        return now()->diffInDays($this->date_fin, false);
    }

    public static function getTarifs(): array
    {
        return [
            1 => 1000,
            3 => 1500,
            7 => 3000,
            14 => 5000,
            30 => 8000,
        ];
    }

    public static function getTarif(int $duree): ?int
    {
        return self::getTarifs()[$duree] ?? null;
    }
}