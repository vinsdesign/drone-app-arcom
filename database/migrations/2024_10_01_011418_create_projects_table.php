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
    if (!Schema::hasTable('projects')) {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('case');
            $table->integer('revenue');
            $table->foreignId('currencies_id')->constrained('currencies')->cascadeOnDelete();
            $table->foreignId('customers_id');
            $table->string('description')->nullable();
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('projects_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('projects_id')->constrained('projects')->cascadeOnDelete();
        });
    }
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
        Schema::dropIfExists('projects_team');
    }
};
