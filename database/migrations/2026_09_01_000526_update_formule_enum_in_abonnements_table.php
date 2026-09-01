<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Étape 1 : Mettre à jour les enregistrements 'premium' vers 'pro'
        DB::table('abonnements')
            ->where('formule', 'premium')
            ->update(['formule' => 'pro']);

        // ✅ Étape 2 : Modifier l'ENUM pour supprimer 'premium'
        DB::statement("ALTER TABLE abonnements MODIFY formule ENUM('basic', 'pro') NOT NULL");
    }

    public function down(): void
    {
        // ✅ En cas de rollback, remettre l'ENUM avec 'premium'
        DB::statement("ALTER TABLE abonnements MODIFY formule ENUM('basic', 'premium', 'pro') NOT NULL");
    }
};