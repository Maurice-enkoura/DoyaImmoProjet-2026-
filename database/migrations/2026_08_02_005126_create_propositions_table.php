<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demande_id')->constrained('demande_immobilieres')->onDelete('cascade');
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->foreignId('bien_id')->constrained('biens_immobiliers')->onDelete('cascade');
            $table->foreignId('particulier_id')->constrained()->onDelete('cascade');
            $table->decimal('prix_propose', 15, 2);
            $table->text('message');
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');
            $table->timestamp('date_proposition')->useCurrent();
            $table->timestamps();

            $table->index('demande_id');
            $table->index('agence_id');
            $table->index('bien_id');
            $table->index('particulier_id');
            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propositions');
    }
};