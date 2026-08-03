<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('particulier_id')->constrained()->onDelete('cascade');
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->integer('note');
            $table->text('commentaire')->nullable();
            $table->timestamp('date_evaluation')->useCurrent();
            $table->text('reponse_agence')->nullable();
            $table->timestamp('date_reponse')->nullable();
            $table->timestamps();

            $table->index('particulier_id');
            $table->index('agence_id');
            $table->index('note');
            $table->unique(['particulier_id', 'agence_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};