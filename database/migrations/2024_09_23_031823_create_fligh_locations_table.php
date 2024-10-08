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
        Schema::create('fligh_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->boolean('draw')->default(false);
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->integer('pos_code');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('altitude');
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('fligh_location_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('fligh_location_id')->constrained('fligh_locations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fligh_locations');
        Schema::dropIfExists('fligh_location_team');
    }
};
