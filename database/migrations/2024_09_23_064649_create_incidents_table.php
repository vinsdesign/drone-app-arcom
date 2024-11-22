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
            $table->string('aircraft_damage')->nullable();
            $table->string('other_damage')->nullable();
            $table->string('description')->nullable();
            $table->string('incuration_type')->nullable();
            $table->string('rectification_note')->nullable();
            $table->date('rectification_date')->nullable();
            $table->string('Technician')->nullable();
            $table->string('status');
            $table->foreignId('location_id')->nullable()->constrained('fligh_locations')->onDelete('set null');
            $table->foreignId('drone_id')->nullable()->constrained('drones')->onDelete('set null');
            // $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('personel_involved_id')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });
        
        Schema::create('incident_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
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
