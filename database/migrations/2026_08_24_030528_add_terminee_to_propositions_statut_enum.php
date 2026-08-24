<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Modifier l'ENUM pour ajouter 'terminee'
        DB::statement("ALTER TABLE propositions MODIFY statut ENUM('en_attente', 'acceptee', 'refusee', 'terminee') DEFAULT 'en_attente'");
    }

    public function down()
    {
        // Revenir à l'ancien ENUM
        DB::statement("ALTER TABLE propositions MODIFY statut ENUM('en_attente', 'acceptee', 'refusee') DEFAULT 'en_attente'");
    }
};