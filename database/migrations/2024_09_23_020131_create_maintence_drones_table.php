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
        Schema::create('maintence_drones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->integer('status');
            $table->integer('cost');
            $table->string('currency');
            $table->string('notes');
            $table->foreignId('drone_id')->constrainedTo('drone')->cascadeDelete();
            $table->foreignId('task_id')->constrainedTo('task')->cascadeDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintence_drones');
    }
};
