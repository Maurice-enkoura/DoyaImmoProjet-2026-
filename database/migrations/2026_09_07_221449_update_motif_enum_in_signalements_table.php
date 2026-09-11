<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Modifier les motifs (si la colonne existe)
        if (Schema::hasColumn('signalements', 'motif')) {
            DB::statement("ALTER TABLE signalements MODIFY motif ENUM('fraude', 'arnaque', 'contenu_inapproprie', 'fausse_annonce', 'comportement_inapproprié', 'autre') NOT NULL");
        }
    }

    public function down(): void
    {
        // ✅ Revenir aux anciens motifs
        if (Schema::hasColumn('signalements', 'motif')) {
            DB::statement("ALTER TABLE signalements MODIFY motif ENUM('fraude', 'contenu_inapproprie', 'arnaque', 'informations_erronees', 'double_annonce', 'autre') NOT NULL");
        }
    }
};