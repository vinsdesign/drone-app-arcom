<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('flighs', function (Blueprint $table) {
            $table->string('locked_flight')->nullable()->default(null)->after('teams_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flighs', function (Blueprint $table) {
            $table->dropColumn('locked_flight');
        });
    }
};
