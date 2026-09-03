<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter slug uniquement s'il n'existe pas déjà
        if (!Schema::hasColumn('agences', 'slug')) {
            Schema::table('agences', function (Blueprint $table) {
                $table->string('slug')->nullable();
            });
        }

        // Générer les slugs pour les agences qui n'en ont pas
        $agences = DB::table('agences')
            ->whereNull('slug')
            ->get();

        foreach ($agences as $agence) {
            $slug = Str::slug($agence->nom_agence . '-' . $agence->id);

            DB::table('agences')
                ->where('id', $agence->id)
                ->update(['slug' => $slug]);
        }

        // Ajouter l'unicité seulement si elle n'existe pas déjà
        $indexes = DB::select("SHOW INDEX FROM agences WHERE Key_name = 'agences_slug_unique'");

        if (empty($indexes)) {
            Schema::table('agences', function (Blueprint $table) {
                $table->unique('slug');
            });
        }

        // Rendre slug obligatoire
        Schema::table('agences', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('agences', 'slug')) {
            Schema::table('agences', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};