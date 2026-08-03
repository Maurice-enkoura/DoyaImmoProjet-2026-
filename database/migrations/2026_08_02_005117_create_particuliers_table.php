<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('particuliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('profession')->nullable();
            $table->string('adresse')->nullable();
            $table->string('quartier')->nullable();
            $table->foreignId('quartier_id')->nullable()->constrained('quartiers')->onDelete('set null');
            $table->timestamps();

            $table->unique('user_id');
            $table->index('user_id');
            $table->index('quartier_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('particuliers');
    }
};