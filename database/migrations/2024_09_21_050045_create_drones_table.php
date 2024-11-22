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
            $table->string('status')->nullable();
            $table->string('idlegal')->nullable();
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('type')->nullable();
            $table->string('serial_p')->nullable();
            $table->string('serial_i')->nullable();
            $table->string('flight_c')->nullable();
            $table->string('remote_c')->nullable();
            $table->string('remote_cc')->nullable();
            $table->string('geometry')->nullable();
            $table->string('inventory_asset')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('users_id')->nullable()->constrained('users')->nullable()->onDelete('set null')->nullable();
            $table->string('firmware_v')->nullable();
            $table->string('hardware_v')->nullable();
            $table->string('propulsion_v')->nullable();
            $table->string('color')->nullable();
            $table->string('remote')->nullable();
            $table->string('conn_card')->nullable();
            $table->integer('initial_flight')->nullable();
            $table->time('initial_flight_time')->nullable();
            $table->time('max_flight_time')->nullable();
            $table->foreignIdFor(Team::class,'teams_id')->index();
            $table->timestamps();
        });
        Schema::create('drone_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('drone_id')->constrained('drones')->cascadeOnDelete();
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
