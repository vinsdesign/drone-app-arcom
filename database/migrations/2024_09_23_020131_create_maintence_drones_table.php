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
        Schema::create('maintence_drones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->integer('status');
            $table->integer('cost');
            $table->foreignId('currencies_id')->constrained('currencies')->cascadeDelete();
            $table->string('notes');
            $table->foreignId('drone_id')->constrainedTo('drone')->cascadeDelete();
            $table->foreignId('task_id')->constrainedTo('task')->cascadeDelete();
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('maintence_drone_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('maintence_drone_id')->constrained('maintence_drones')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintence_drones');
        Schema::dropIfExists('maintence_drone_team');
    }
};
