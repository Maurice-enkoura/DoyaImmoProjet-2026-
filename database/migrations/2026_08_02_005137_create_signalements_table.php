<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signalements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('particulier_id')->constrained()->onDelete('cascade');
            $table->morphs('signalable');
            $table->enum('motif', ['fraude', 'contenu_inapproprie', 'arnaque', 'informations_erronees', 'double_annonce', 'autre']);
            $table->text('description');
            $table->enum('statut', ['en_attente', 'traite', 'rejete'])->default('en_attente');
            $table->timestamp('date_signalement')->useCurrent();
            $table->timestamp('date_traitement')->nullable();
            $table->text('commentaire_admin')->nullable();
            $table->timestamps();

            $table->index('particulier_id');
            $table->index('statut');
            $table->index('motif');
            $table->index(['signalable_id', 'signalable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signalements');
    }
};