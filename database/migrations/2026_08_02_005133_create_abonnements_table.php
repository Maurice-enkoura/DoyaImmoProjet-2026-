<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abonnements', function (Blueprint $table) {
    $table->id();

    $table->foreignId('agence_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->enum('formule', ['basic', 'premium', 'pro']);

    $table->decimal('montant', 10, 2);

    $table->dateTime('date_debut')->useCurrent();
    $table->dateTime('date_fin')->nullable();

    $table->boolean('statut')->default(true);

    $table->timestamps();

    $table->index('agence_id');
    $table->index('formule');
    $table->index('statut');
    $table->index('date_fin');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};