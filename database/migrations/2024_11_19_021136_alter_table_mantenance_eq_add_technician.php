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
        Schema::table('maintence_eqs', function (Blueprint $table) {
            $table->string('technician')->nullable()->default(null)->after('teams_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintence_eqs', function (Blueprint $table) {
            $table->dropColumn('technician');
        });
    }
};
