<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_agences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->enum('type_document', ['rccm', 'ninea', 'piece_identite', 'logo']);
            $table->string('nom_fichier');
            $table->enum('statut_validation', ['en_attente', 'valide', 'rejete'])->default('en_attente');
            $table->foreignId('valide_par')->nullable()->constrained('administrateurs')->onDelete('set null');
            $table->timestamp('date_validation')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->index('agence_id');
            $table->index('type_document');
            $table->index('statut_validation');
            $table->unique(['agence_id', 'type_document']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_agences');
    }
};