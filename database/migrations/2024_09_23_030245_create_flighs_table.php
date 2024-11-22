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
            $table->dateTime('start_date_flight');
            $table->dateTime('end_date_flight');
            $table->time('duration');
            $table->string('type');
            $table->string('ops');
            $table->integer('landings')->default('1');
            $table->foreignId('customers_id')->nullable()->constrained('customers')->onDelete('set null');
            // $table->foreignId('location_id')->references('id')->on('fligh_locations')->onDelete('set null')->default(null);
            // $table->foreignId('projects_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('kits_id')->nullable()->constrained('kits')->onDelete('set null');
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('vo')->nullable();
            $table->string('po')->nullable();
            $table->string('instructor')->nullable()->references('id')->on('users')->onDelete('set null');

            $table->foreignId('drones_id')->nullable()->constrained('drones')->onDelete('set null');
            $table->integer('pre_volt')->nullable();
            $table->integer('fuel_used')->nullable()->default('1');
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