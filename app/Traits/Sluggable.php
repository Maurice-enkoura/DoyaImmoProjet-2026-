<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait Sluggable
{
    /**
     * Boot the sluggable trait.
     */
    protected static function bootSluggable()
    {
        static::creating(function ($model) {
            $model->generateSlug();
        });

        static::updating(function ($model) {
            if ($model->isDirty($model->getSlugSourceField())) {
                $model->generateSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the model.
     */
    protected function generateSlug(): void
    {
        $source = $this->getSlugSource();
        
        // ✅ SI LA SOURCE EST NULLE, UTILISER UNE VALEUR PAR DÉFAUT
        if (empty($source)) {
            $source = 'demande-' . uniqid();
        }
        
        $slug = Str::slug($source);

        $count = 0;
        $newSlug = $slug;

        while ($this->slugExists($newSlug)) {
            $count++;
            $newSlug = $slug . '-' . $count;
        }

        $this->slug = $newSlug;
    }

    /**
     * Check if a slug already exists.
     */
    protected function slugExists(string $slug): bool
    {
        return static::where('slug', $slug)
            ->where('id', '!=', $this->id ?? 0)
            ->exists();
    }

    /**
     * Get the source field for slug generation.
     * ✅ MODIFIÉ : Gère le cas où l'attribut n'existe pas encore
     */
    protected function getSlugSource(): string
    {
        $field = $this->getSlugSourceField();
        $value = $this->getAttribute($field);
        
        // ✅ SI LA VALEUR EST NULLE, RETOURNER UNE CHAÎNE VIDE
        return $value ?? '';
    }

    /**
     * Get the field name used for slug generation.
     */
    protected function getSlugSourceField(): string
    {
        return $this->slugSource ?? 'titre';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}