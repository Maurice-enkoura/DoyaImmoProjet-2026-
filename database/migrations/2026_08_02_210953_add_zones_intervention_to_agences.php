<?php
// database/migrations/2024_01_01_000015_add_zones_intervention_to_agences.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->json('zones_intervention')->nullable()->after('quartier');
        });
    }

    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn('zones_intervention');
        });
    }
};