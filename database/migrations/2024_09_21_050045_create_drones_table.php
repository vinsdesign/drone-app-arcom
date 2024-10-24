<?php

use App\Models\Team;
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
            $table->string('geometry');
            $table->string('inventory_asset'); // nanti hapus
            $table->string('description');
            $table->foreignId('users_id')->constrained('users')->cascadeOnDelete();
            $table->string('firmware_v');
            $table->string('hardware_v');
            $table->string('propulsion_v');
            $table->string('color');
            $table->string('remote');
            $table->string('conn_card');
            $table->integer('initial_flight')->nullable();
            $table->time('initial_flight_time')->nullable();
            $table->time('max_flight_time')->nullable();
            $table->foreignIdFor(Team::class,'teams_id')->index();
            $table->timestamps();
        });
        Schema::create('drone_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams');
            $table->foreignId('drone_id')->constrained('drones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drones');
        Schema::dropIfExists('drone_team');
    }
};
