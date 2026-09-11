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
        
        // ✅ SI LA SOURCE EST NULLE OU VIDE, UTILISER UN ID UNIQUE
        if (empty($source) || $source === '') {
            $source = 'item-' . uniqid();
        }
        
        // ✅ LIMITER LA LONGUEUR DU SLUG À 200 CARACTÈRES
        $slug = Str::slug($source);
        
        // ✅ SI LE SLUG EST TROP LONG, LE TRONQUER
        if (strlen($slug) > 200) {
            $slug = substr($slug, 0, 200);
        }
        
        // ✅ SI LE SLUG EST VIDE APRÈS LE TRONCAGE, UTILISER UN ID
        if (empty($slug)) {
            $slug = 'item-' . uniqid();
        }

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
        // ✅ VÉRIFIER SI L'ATTRIBUT EXISTE
        if (isset($this->slugSource)) {
            return $this->slugSource;
        }
        
        // ✅ LISTE DES CHAMPS POSSIBLES POUR LE SLUG
        $possibleFields = ['titre', 'nom_agence', 'zone_recherchee', 'type_bien'];
        
        foreach ($possibleFields as $field) {
            if (isset($this->$field) && !empty($this->$field)) {
                return $field;
            }
        }
        
        // ✅ PAR DÉFAUT : RETOURNER UN ID
        return 'id';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}