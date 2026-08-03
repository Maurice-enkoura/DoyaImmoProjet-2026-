<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposition_id')->constrained()->onDelete('cascade');
            $table->foreignId('particulier_id')->constrained()->onDelete('cascade');
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->date('date_visite');
            $table->time('heure_visite');
            $table->enum('statut', ['planifie', 'confirme', 'annule', 'termine'])->default('planifie');
            $table->timestamps();

            $table->index('proposition_id');
            $table->index('particulier_id');
            $table->index('agence_id');
            $table->index('statut');
            $table->index('date_visite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};