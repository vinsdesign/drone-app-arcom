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
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['personel_involved_id']);
            $table->dropColumn('personel_involved_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('personel_involved_id');
            $table->foreign('personel_involved_id')->references('id')->on('users');
        });
    }
};
