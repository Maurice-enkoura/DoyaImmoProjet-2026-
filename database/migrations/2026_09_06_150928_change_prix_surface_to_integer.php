<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // ✅ Pour MySQL - Approche avec colonnes temporaires
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            // 1. Ajouter des colonnes temporaires
            $table->integer('prix_temp')->nullable();
            $table->integer('surface_temp')->nullable();
        });

        // 2. Copier les données converties
        DB::statement('UPDATE biens_immobiliers SET prix_temp = CAST(prix AS UNSIGNED)');
        DB::statement('UPDATE biens_immobiliers SET surface_temp = CAST(surface AS UNSIGNED)');

        // 3. Supprimer les anciennes colonnes
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->dropColumn('prix');
            $table->dropColumn('surface');
        });

        // 4. Renommer les colonnes temporaires
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->renameColumn('prix_temp', 'prix');
            $table->renameColumn('surface_temp', 'surface');
        });
    }

    public function down()
    {
        // ✅ Revenir en decimal
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->decimal('prix', 15, 2)->nullable();
            $table->decimal('surface', 10, 2)->nullable();
        });

        // Copier les données
        DB::statement('UPDATE biens_immobiliers SET prix = CAST(prix AS DECIMAL(15,2))');
        DB::statement('UPDATE biens_immobiliers SET surface = CAST(surface AS DECIMAL(10,2))');
    }
};