<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->boolean('climatisation')->default(false)->after('est_meuble');
            $table->boolean('balcon')->default(false)->after('climatisation');
            $table->boolean('jardin')->default(false)->after('balcon');
            $table->boolean('piscine')->default(false)->after('jardin');
            $table->boolean('ascenseur')->default(false)->after('piscine');
            $table->boolean('securite')->default(false)->after('ascenseur');
        });
    }

    public function down(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->dropColumn(['climatisation', 'balcon', 'jardin', 'piscine', 'ascenseur', 'securite']);
        });
    }
};