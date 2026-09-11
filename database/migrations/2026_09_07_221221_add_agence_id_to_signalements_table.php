<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Vérifier si la colonne n'existe pas déjà
        if (!Schema::hasColumn('signalements', 'agence_id')) {
            Schema::table('signalements', function (Blueprint $table) {
                // ✅ Ajouter la colonne agence_id
                $table->foreignId('agence_id')
                    ->nullable()
                    ->after('particulier_id')
                    ->constrained()
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        // ✅ Supprimer la colonne si nécessaire
        if (Schema::hasColumn('signalements', 'agence_id')) {
            Schema::table('signalements', function (Blueprint $table) {
                $table->dropForeign(['agence_id']);
                $table->dropColumn('agence_id');
            });
        }
    }
};