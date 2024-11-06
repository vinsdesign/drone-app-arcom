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
        Schema::create('kits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->nullable();
            $table->boolean('enabled')->nullable()->default(false);
            $table->foreignId('blocked')->nullable()->constrained('drones')->default('null')->onDelete('set null');
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('kits_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('kits_id')->constrained('kits')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kits');
        Schema::dropIfExists('kits_team');
    }
};
