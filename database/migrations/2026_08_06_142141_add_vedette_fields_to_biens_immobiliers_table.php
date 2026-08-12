<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->boolean('est_vedette')->default(false)->after('statut');
            $table->timestamp('vedette_debut')->nullable()->after('est_vedette');
            $table->timestamp('vedette_fin')->nullable()->after('vedette_debut');
            $table->integer('vedette_duree')->default(7)->after('vedette_fin'); 
        });
    }

    public function down(): void
    {
        Schema::table('biens_immobiliers', function (Blueprint $table) {
            $table->dropColumn(['est_vedette', 'vedette_debut', 'vedette_fin', 'vedette_duree']);
        });
    }
};