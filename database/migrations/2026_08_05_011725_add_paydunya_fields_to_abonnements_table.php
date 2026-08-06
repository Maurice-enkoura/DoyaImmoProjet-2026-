<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->string('paydunya_token')->nullable()->after('statut');
            $table->string('paydunya_status')->nullable()->after('paydunya_token');
            $table->timestamp('paydunya_paid_at')->nullable()->after('paydunya_status');
        });
    }

    public function down()
    {
        Schema::table('abonnements', function (Blueprint $table) {
            $table->dropColumn(['paydunya_token', 'paydunya_status', 'paydunya_paid_at']);
        });
    }
};