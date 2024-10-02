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
        Schema::table('maintence_drones', function (Blueprint $table) {
            $table->string('part')->nullable();
            $table->string('part_name')->nullable();
            $table->string('status_part')->nullable();
            $table->string('technician')->nullable();
            $table->string('new_part_serial')->nullable();
            $table->string('description_part')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maintence_drones', function (Blueprint $table) {
            //
        });
    }
};
