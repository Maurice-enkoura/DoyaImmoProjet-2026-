<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->integer('nombre_salles_bain')->nullable()->after('nombre_chambres');
        });
    }

    public function down(): void
    {
        Schema::table('demande_immobilieres', function (Blueprint $table) {
            $table->dropColumn('nombre_salles_bain');
        });
    }
};