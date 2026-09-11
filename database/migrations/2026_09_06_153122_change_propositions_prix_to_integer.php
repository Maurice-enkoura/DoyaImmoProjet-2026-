<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ✅ Table propositions
        Schema::table('propositions', function (Blueprint $table) {
            // 1. Ajouter une colonne temporaire
            $table->integer('prix_propose_temp')->nullable();
        });

        // 2. Copier les données converties
        DB::statement('UPDATE propositions SET prix_propose_temp = CAST(prix_propose AS UNSIGNED)');

        // 3. Supprimer l'ancienne colonne
        Schema::table('propositions', function (Blueprint $table) {
            $table->dropColumn('prix_propose');
        });

        // 4. Renommer la colonne temporaire
        Schema::table('propositions', function (Blueprint $table) {
            $table->renameColumn('prix_propose_temp', 'prix_propose');
        });
    }

    public function down(): void
    {
        // ✅ Revenir en decimal
        Schema::table('propositions', function (Blueprint $table) {
            $table->decimal('prix_propose', 15, 2)->nullable();
        });

        DB::statement('UPDATE propositions SET prix_propose = CAST(prix_propose AS DECIMAL(15,2))');
    }
};