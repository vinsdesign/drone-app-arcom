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
        Schema::table('battreis', function (Blueprint $table) {
            $table->integer('life_span_cycle')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battreis', function (Blueprint $table) {
            $table->dropColumn('life_span_cycle');
        });
    }
};
