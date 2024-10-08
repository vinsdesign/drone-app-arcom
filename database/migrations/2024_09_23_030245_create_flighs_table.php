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
        Schema::create('flighs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date_flight');
            $table->integer('duration_hour');
            $table->integer('duration_minute');
            $table->string('type');
            $table->string('ops');
            $table->integer('landings')->default('1');
            $table->foreignId('customers_id')->constrainedTo('customers')->cascadeDelete();
            //$table->foreignId('location_id')->constrainedTo('fligh_location')->cascadeDelete()->default(null);
            $table->foreignId('projects_id')->constrainedTo('projects')->cascadeDelete();
            $table->foreignId('users_id')->constrainedTo('users')->cascadeDelete();
            $table->string('vo');
            $table->string('po');
            $table->string('instructor');
            $table->foreignId('drones_id')->constrainedTo('drones')->cascadeDelete();
            $table->foreignId('battreis_id')->constrainedTo('battreis')->cascadeDelete();
            $table->foreignId('equidments_id')->constrainedTo('equidments')->cascadeDelete();
            $table->integer('pre_volt');
            $table->integer('fuel_used')->default('1');
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            //$table->foreignId('wheater_id')->constrainedTo('wheater')->cascadeDelete();
            $table->timestamps();
        });
        Schema::create('fligh_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('fligh_id')->constrained('flighs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flighs');
        Schema::dropIfExists('fligh_team');
    }
};
