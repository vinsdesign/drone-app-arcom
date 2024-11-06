<?php

use App\Models\team;
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
        Schema::create('planned_missions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('start_date_flight');
            $table->dateTime('end_date_flight');
            $table->time('duration');
            $table->string('type');
            $table->string('ops');
            $table->integer('landings')->default('1');
            $table->foreignId('customers_id')->constrainedTo('customers')->cascadeDelete();
            $table->foreignId('location_id')->constrainedTo('fligh_locations')->cascadeDelete()->default(null);
            $table->foreignId('projects_id')->constrainedTo('projects')->cascadeDelete();
            $table->foreignId('kits_id')->nullable()->constrainedTo('kits')->cascadeDelete();
            $table->foreignId('users_id')->constrainedTo('users')->cascadeDelete();
            $table->foreignId('drones_id')->constrainedTo('drones')->cascadeDelete();
            $table->integer('pre_volt');
            $table->integer('fuel_used')->default('1');
            $table->foreignIdFor(team::class,'teams_id')->index()->cascadeOnDelete();
            //$table->foreignId('wheater_id')->constrainedTo('wheater')->cascadeDelete();
            $table->timestamps();
        });
        Schema::create('planned_mission_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('planned_mission_id')->constrained('planned_missions')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_missions');
    }
};
