<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ 1. Vérifier si la colonne existe déjà
        $hasColumn = Schema::hasColumn('demande_immobilieres', 'slug');
        
        // ✅ 2. Si la colonne n'existe pas, l'ajouter
        if (!$hasColumn) {
            Schema::table('demande_immobilieres', function (Blueprint $table) {
                $table->string('slug', 255)->nullable();
            });
        }

        // ✅ 3. Supprimer l'ancienne contrainte unique si elle existe
        try {
            Schema::table('demande_immobilieres', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        } catch (\Exception $e) {
            // La contrainte n'existe pas, on continue
        }

        // ✅ 4. Générer des slugs courts pour les demandes existantes
        $demandes = DB::table('demande_immobilieres')
            ->whereNull('slug')
            ->get();

        foreach ($demandes as $demande) {
            $slug = 'demande-' . $demande->id . '-' . Str::random(6);
            
            // ✅ Vérifier l'unicité du slug
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('demande_immobilieres')->where('slug', $slug)->where('id', '!=', $demande->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            DB::table('demande_immobilieres')
                ->where('id', $demande->id)
                ->update(['slug' => $slug]);
        }

        // ✅ 5. Rendre slug NOT NULL et ajouter contrainte unique
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->string('slug', 255)->nullable(false)->change();
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        // ✅ Supprimer la contrainte unique avant de supprimer la colonne
        try {
            Schema::table('demande_immobilieres', function (Blueprint $table) {
                $table->dropUnique(['slug']);
            });
        } catch (\Exception $e) {
            // La contrainte n'existe pas, on continue
        }

        if (Schema::hasColumn('demande_immobilieres', 'slug')) {
            Schema::table('demande_immobilieres', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};