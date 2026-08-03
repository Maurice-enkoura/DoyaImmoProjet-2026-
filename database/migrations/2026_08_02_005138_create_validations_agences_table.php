<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validations_agences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->foreignId('administrateur_id')->constrained('administrateurs')->onDelete('cascade');
            $table->boolean('statut')->default(false);
            $table->text('commentaire')->nullable();
            $table->timestamp('date_validation')->useCurrent();
            $table->timestamps();

            $table->index('agence_id');
            $table->index('administrateur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validations_agences');
    }
};