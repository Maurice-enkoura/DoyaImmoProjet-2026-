<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter slug uniquement s'il n'existe pas déjà
        if (!Schema::hasColumn('demande_immobilieres', 'slug')) {
            Schema::table('demande_immobilieres', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable();
            });
        }

        // Générer les slugs pour les demandes existantes
        $demandes = DB::table('demande_immobilieres')
            ->whereNull('slug')
            ->get();

        foreach ($demandes as $demande) {
            $slug = Str::slug(
                $demande->type_bien . '-' .
                $demande->zone_recherchee . '-' .
                $demande->id
            );

            DB::table('demande_immobilieres')
                ->where('id', $demande->id)
                ->update(['slug' => $slug]);
        }

        // Rendre slug obligatoire
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('demande_immobilieres', 'slug')) {
            Schema::table('demande_immobilieres', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};