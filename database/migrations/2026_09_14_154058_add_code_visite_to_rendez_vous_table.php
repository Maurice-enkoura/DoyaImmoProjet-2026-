<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            // Code unique à 6 chiffres généré automatiquement
            $table->string('code_visite', 6)->nullable()->after('heure_visite');

            // Validation par l'agence le jour J
            $table->boolean('code_valide')->default(false)->after('code_visite');
            $table->timestamp('code_valide_le')->nullable()->after('code_valide');
        });
    }

    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropColumn(['code_visite', 'code_valide', 'code_valide_le']);
        });
    }
};