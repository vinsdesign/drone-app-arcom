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
        Schema::create('planned_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planned_id')->constrainedTo('planned_missions')->cascadeDelete();
            $table->foreignId('equidment_id')->constrainedTo('equidment')->cascadeDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_equipment');
    }
};
