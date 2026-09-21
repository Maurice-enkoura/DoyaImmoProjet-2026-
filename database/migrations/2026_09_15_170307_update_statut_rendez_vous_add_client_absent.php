<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ⚠️ On utilise DB::statement car modifier un ENUM existant n'est pas
        // supporté nativement par le Schema Builder de Laravel.
        DB::statement("ALTER TABLE rendez_vous 
            MODIFY COLUMN statut ENUM(
                'planifie', 
                'confirme', 
                'annule', 
                'termine', 
                'client_absent'
            ) NOT NULL DEFAULT 'planifie'");
    }

    public function down(): void
    {
        // On retire 'client_absent'
        // ⚠️ Vérifie qu'aucun RDV n'a ce statut avant de rollback
        DB::statement("ALTER TABLE rendez_vous 
            MODIFY COLUMN statut ENUM(
                'planifie', 
                'confirme', 
                'annule', 
                'termine'
            ) NOT NULL DEFAULT 'planifie'");
    }
};