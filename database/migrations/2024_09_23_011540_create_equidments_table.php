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
        Schema::create('equidments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('status');
            $table->boolean('inventory_asset')->default(false);
            $table->integer('serial');
            $table->string('type');
            $table->foreignId('for_drone')->nullable()->constrained('drones')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('purchase_date');
            $table->integer('insurable_value');
            $table->string('firmware_v');
            $table->string('hardware_v');
            $table->boolean('is_loaner')->default(false);
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equidments');
    }
};
