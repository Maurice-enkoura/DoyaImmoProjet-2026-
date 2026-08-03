<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demande_immobilieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('particulier_id')->constrained()->onDelete('cascade');
            $table->enum('type_operation', ['achat', 'location']);
            $table->enum('type_bien', ['appartement', 'studio', 'villa', 'maison', 'terrain', 'bureau']);
            $table->decimal('budget_maximum', 15, 2);
            $table->string('zone_recherchee');
            $table->foreignId('quartier_id')->nullable()->constrained('quartiers')->onDelete('set null');
            $table->integer('nombre_chambres')->nullable();
            $table->decimal('surface_minimum', 10, 2)->nullable();
            $table->date('date_entree_souhaitee')->nullable();
            $table->text('criteres_particuliers')->nullable();
            $table->text('description');
            $table->enum('statut', ['en_attente', 'en_cours', 'terminee', 'annulee'])->default('en_attente');
            $table->timestamp('date_publication')->useCurrent();
            $table->timestamps();

            $table->index('particulier_id');
            $table->index('type_operation');
            $table->index('type_bien');
            $table->index('statut');
            $table->index('zone_recherchee');
            $table->index('quartier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demande_immobilieres');
    }
};