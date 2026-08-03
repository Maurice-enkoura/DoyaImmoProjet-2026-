<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->morphs('mediable');
            $table->enum('type_media', ['image', 'video']);
            $table->string('fichier');
            $table->timestamps();

            $table->index('agence_id');
            $table->index('type_media');
            $table->index(['mediable_id', 'mediable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};