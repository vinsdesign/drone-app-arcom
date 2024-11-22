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
            $table->date('date')->nullable();
            $table->integer('status')->nullable();
            $table->integer('cost')->nullable();
            $table->foreignId('currencies_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->string('notes')->nullable();
            $table->foreignId('drone_id')->nullable()->constrained('drones')->onDelete('set null');
            // $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('set null');
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
