<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biens_immobiliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->string('titre');
            $table->enum('type_bien', ['appartement', 'studio', 'villa', 'maison', 'terrain', 'bureau']);
            $table->enum('type_contrat', ['vente', 'location']);
            $table->decimal('prix', 15, 2);
            $table->string('quartier');
            $table->foreignId('quartier_id')->nullable()->constrained('quartiers')->onDelete('set null');
            $table->string('adresse');
            $table->integer('nombre_chambres')->default(0);
            $table->integer('nombre_salles_bain')->default(0);
            $table->decimal('surface', 10, 2);
            $table->boolean('parking_disponible')->default(false);
            $table->boolean('est_meuble')->default(false);
            $table->text('description');
            $table->boolean('statut')->default(true);
            $table->timestamps();

            $table->index('agence_id');
            $table->index('type_bien');
            $table->index('type_contrat');
            $table->index('statut');
            $table->index('quartier');
            $table->index('quartier_id');
            $table->index('prix');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biens_immobiliers');
    }
};