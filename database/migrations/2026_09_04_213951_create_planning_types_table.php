<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('planning_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained()->onDelete('cascade');
            $table->integer('jour'); // 0 = Lundi, 6 = Dimanche
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('planning_types');
    }
};