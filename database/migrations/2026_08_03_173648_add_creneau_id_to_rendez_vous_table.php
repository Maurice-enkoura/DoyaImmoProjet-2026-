<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->foreignId('creneau_id')
                ->nullable()
                ->after('agence_id')
                ->constrained('creneaux_rendez_vous')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropForeign(['creneau_id']);
            $table->dropColumn('creneau_id');
        });
    }
};