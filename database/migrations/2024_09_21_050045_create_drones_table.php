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
        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('idlegal');
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->integer('serial_p');
            $table->integer('serial_i');
            $table->string('flight_c');
            $table->string('remote_c');
            $table->string('remote_cc');
            $table->foreignId('inventory_id')->constrainedTo('inventory')->cascadeDelete();
            $table->string('inventory_asset'); // nanti hapus
            $table->string('description');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('firmware_v');
            $table->string('hardware_v');
            $table->string('propulsion_v');
            $table->string('color');
            $table->string('remote');
            $table->string('conn_card');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drones');
    }
};
