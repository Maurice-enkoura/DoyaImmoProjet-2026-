<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->integer('vues')->default(0)->after('statut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->dropColumn('vues');
        });
    }
};