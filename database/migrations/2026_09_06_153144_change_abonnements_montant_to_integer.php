<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Table abonnements
        Schema::table('abonnements', function (Blueprint $table) {
            // 1. Ajouter une colonne temporaire
            $table->integer('montant_temp')->nullable();
        });

        // 2. Copier les données converties
        DB::statement('UPDATE abonnements SET montant_temp = CAST(montant AS UNSIGNED)');

        // 3. Supprimer l'ancienne colonne
        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropColumn('montant');
        });

        // 4. Renommer la colonne temporaire
        Schema::table('abonnements', function (Blueprint $table) {
            $table->renameColumn('montant_temp', 'montant');
        });
    }

    public function down(): void
    {
        // ✅ Revenir en decimal
        Schema::table('abonnements', function (Blueprint $table) {
            $table->decimal('montant', 10, 2)->nullable();
        });

        DB::statement('UPDATE abonnements SET montant = CAST(montant AS DECIMAL(10,2))');
    }
};