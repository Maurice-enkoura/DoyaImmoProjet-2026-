<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            if (!Schema::hasColumn('signalements', 'agence_id')) {
                $table->foreignId('agence_id')->nullable()->after('particulier_id')->constrained('agences')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('signalements', function (Blueprint $table) {
            if (Schema::hasColumn('signalements', 'agence_id')) {
                $table->dropForeign(['agence_id']);
                $table->dropColumn('agence_id');
            }
        });
    }
};