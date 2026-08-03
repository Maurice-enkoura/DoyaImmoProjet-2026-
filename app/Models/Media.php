<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Enums\TypeMediaEnum;

class Media extends Model
{
    use HasFactory;

    // Spécifier explicitement le nom de la table
    protected $table = 'medias';

    protected $fillable = [
        'agence_id',
        'mediable_id',
        'mediable_type',
        'type_media',
        'fichier',
    ];

    protected $casts = [
        'type_media' => TypeMediaEnum::class,
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeImages($query)
    {
        return $query->where('type_media', TypeMediaEnum::IMAGE);
    }

    public function scopeVideos($query)
    {
        return $query->where('type_media', TypeMediaEnum::VIDEO);
    }
}