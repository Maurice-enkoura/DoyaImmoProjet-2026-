<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            // Ajouter les champs pour le refus
            $table->boolean('est_refusee')->default(false)->after('statut_validation');
            $table->text('motif_refus')->nullable()->after('est_refusee');
            $table->timestamp('date_refus')->nullable()->after('motif_refus');
        });
    }

    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn(['est_refusee', 'motif_refus', 'date_refus']);
        });
    }
};