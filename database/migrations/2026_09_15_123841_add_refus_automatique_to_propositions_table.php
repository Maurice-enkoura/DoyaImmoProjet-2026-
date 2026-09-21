<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('propositions', function (Blueprint $table) {
            $table->boolean('refus_automatique')
                  ->default(false)
                  ->after('statut');
        });
    }

    public function down(): void
    {
        Schema::table('propositions', function (Blueprint $table) {
            $table->dropColumn('refus_automatique');
        });
    }
};