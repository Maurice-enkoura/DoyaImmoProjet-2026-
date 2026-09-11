<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Table demande_immobilieres
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            // 1. Ajouter des colonnes temporaires
            $table->integer('budget_maximum_temp')->nullable();
            $table->integer('surface_minimum_temp')->nullable();
        });

        // 2. Copier les données converties
        DB::statement('UPDATE demande_immobilieres SET budget_maximum_temp = CAST(budget_maximum AS UNSIGNED)');
        DB::statement('UPDATE demande_immobilieres SET surface_minimum_temp = CAST(surface_minimum AS UNSIGNED)');

        // 3. Supprimer les anciennes colonnes
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->dropColumn('budget_maximum');
            $table->dropColumn('surface_minimum');
        });

        // 4. Renommer les colonnes temporaires
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->renameColumn('budget_maximum_temp', 'budget_maximum');
            $table->renameColumn('surface_minimum_temp', 'surface_minimum');
        });
    }

    public function down(): void
    {
        // ✅ Revenir en decimal
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->decimal('budget_maximum', 15, 2)->nullable();
            $table->decimal('surface_minimum', 10, 2)->nullable();
        });

        DB::statement('UPDATE demande_immobilieres SET budget_maximum = CAST(budget_maximum AS DECIMAL(15,2))');
        DB::statement('UPDATE demande_immobilieres SET surface_minimum = CAST(surface_minimum AS DECIMAL(10,2))');
    }
};