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
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('equidment_id')->nullable()->constrained('equidments')->onDelete('set null');
            $table->foreignId('battrei_id')->nullable()->constrained('battreis')->onDelete('set null');
            $table->foreignId('drone_id')->nullable()->constrained('drones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('equidment_id');
            $table->dropColumn('battrei_id');
            $table->dropColumn('drone_id');
        });
    }
};
