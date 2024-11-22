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
            $table->string('description')->nullable();
            $table->boolean('draw')->nullable()->default(false);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->integer('pos_code')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('altitude')->nullable();
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            // $table->foreignId('projects_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->foreignId('customers_id')->nullable()->constrained('customers')->onDelete('set null');
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
