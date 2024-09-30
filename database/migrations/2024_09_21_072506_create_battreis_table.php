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
        Schema::create('battreis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('status');
            $table->string('asset_inventory');
            $table->integer('serial_P');
            $table->integer('serial_I');
            $table->integer('cellCount');
            $table->integer('nominal_voltage');
            $table->integer('capacity');
            $table->integer('initial_Cycle_count');
            $table->integer('life_span');
            $table->integer('flaight_count');
            $table->foreignId('for_drone')->constrainedTo('drone')->cascadeDelete();
            $table->date('purchase_date');
            $table->integer('insurable_value');
            $table->integer('wight');
            $table->string('firmware_version');
            $table->string('hardware_version');
            $table->boolean('is_loaner')->default(false);
            $table->string('description');
            $table->foreignId('owner_id')->constrainedTo('users')->cascadeDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battreis');
    }
};
