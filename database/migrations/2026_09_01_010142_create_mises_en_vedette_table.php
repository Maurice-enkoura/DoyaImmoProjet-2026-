<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mises_en_vedette', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens_immobiliers')->onDelete('cascade');
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->integer('duree')->comment('Durée en jours: 1, 3, 7, 14, 30');
            $table->decimal('montant', 12, 0);
            $table->timestamp('date_debut')->nullable();
            $table->timestamp('date_fin')->nullable();
            $table->string('statut')->default('en_attente')->comment('en_attente, actif, expire, annule');
            $table->text('commentaire_admin')->nullable();
            $table->timestamp('validee_par_admin_at')->nullable();
            $table->foreignId('validee_par_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['bien_id', 'statut']);
            $table->index(['agence_id', 'statut']);
            $table->index('date_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mises_en_vedette');
    }
};