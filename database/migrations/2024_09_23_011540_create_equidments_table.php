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
        Schema::create('equidments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->string('status');
            $table->string('inventory_asset');
            $table->integer('serial');
            $table->string('type');
            $table->foreignId('drones_id')->constrained('drones')->cascadeOnDelete();
            $table->foreignId('users_id')->constrained('users')->cascadeOnDelete();
            $table->date('purchase_date');
            $table->integer('insurable_value');
            $table->integer('weight');
            $table->string('firmware_v');
            $table->string('hardware_v');
            $table->boolean('is_loaner')->default(false);
            $table->string('description');
            $table->foreignIdFor(Team::class,'teams_id')->index();
            $table->timestamps();
        });

        Schema::create('equidment_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams');
            $table->foreignId('equidment_id')->constrained('equidments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equidments');
        Schema::dropIfExists('equidment_team');
    }
};
