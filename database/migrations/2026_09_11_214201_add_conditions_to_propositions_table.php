<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propositions', function (Blueprint $table) {
            // Disponibilité du bien proposée par l'agence
            $table->date('disponibilite')->nullable()->after('prix_propose');

            // Conditions financières
            $table->integer('caution_mois')->nullable()->after('disponibilite');
            $table->integer('avance_loyer_mois')->nullable()->after('caution_mois');
            $table->integer('honoraires')->nullable()->after('avance_loyer_mois');
            $table->integer('charges')->nullable()->after('honoraires');

            // Conditions libres (bail mini 12 mois, etc.)
            $table->text('conditions_particulieres')->nullable()->after('charges');

            // Index (utile si on veut filtrer plus tard)
            $table->index('disponibilite');
        });
    }

    public function down(): void
    {
        Schema::table('propositions', function (Blueprint $table) {
            $table->dropIndex(['disponibilite']);
            $table->dropColumn([
                'disponibilite',
                'caution_mois',
                'avance_loyer_mois',
                'honoraires',
                'charges',
                'conditions_particulieres',
            ]);
        });
    }
};