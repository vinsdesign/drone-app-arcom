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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->date('incident_date');
            $table->string('cause');
            $table->string('aircraft_damage');
            $table->string('other_damage');
            $table->string('description');
            $table->string('incuration_type');
            $table->string('rectification_note');
            $table->date('rectification_date');
            $table->string('Technician');
            $table->foreignId('location_id')->constrainedTo('location')->cascadeDelete();
            $table->foreignId('drone_id')->constrainedTo('drone')->cascadeDelete();
            $table->foreignId('project_id')->constrainedTo('project')->cascadeDelete();
            $table->foreignId('personel_involved_id')->constrainedTo('users')->cascadeDelete();
            $table->foreignIdFor(Team::class,'teams_id')->index();
            $table->timestamps();
        });
        
        Schema::create('incident_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams');
            $table->foreignId('incident_id')->constrained('incidents');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
        Schema::dropIfExists('incident_team');
    }
};
