<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter slug uniquement s'il n'existe pas déjà
        if (!Schema::hasColumn('biens_immobiliers', 'slug')) {
            Schema::table('biens_immobiliers', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable()->after('titre');
            });
        }

        // Générer les slugs pour les biens qui n'en ont pas
        $biens = DB::table('biens_immobiliers')
            ->whereNull('slug')
            ->get();

        foreach ($biens as $bien) {
            $slug = Str::slug($bien->titre . '-' . $bien->id);

            DB::table('biens_immobiliers')
                ->where('id', $bien->id)
                ->update(['slug' => $slug]);
        }

        // Rendre slug obligatoire
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('biens_immobiliers', 'slug')) {
            Schema::table('biens_immobiliers', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};