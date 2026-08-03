<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creneaux_rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->boolean('est_disponible')->default(true);
            $table->timestamps();

            $table->index(['agence_id', 'date']);
            $table->index('est_disponible');
            $table->unique(['agence_id', 'date', 'heure_debut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creneaux_rendez_vous');
    }
};