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
        Schema::create('battreis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('status')->nullable();
            $table->string('asset_inventory')->nullable();
            $table->string('serial_P')->nullable();
            $table->string('serial_I')->nullable();
            $table->integer('cellCount')->nullable();
            $table->integer('nominal_voltage')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('initial_Cycle_count')->nullable();
            $table->integer('life_span')->nullable();
            $table->integer('flaight_count')->nullable();
            $table->foreignId('for_drone')->nullable()->constrained('drones')->onDelete('set null');
            $table->date('purchase_date')->nullable();
            $table->integer('insurable_value')->nullable();
            $table->integer('wight')->nullable();
            $table->string('firmware_version')->nullable();
            $table->string('hardware_version')->nullable();
            $table->boolean('is_loaner')->nullable()->default(false)->nullable();
            $table->string('description')->nullable();
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('battrei_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('battrei_id')->constrained('battreis')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battreis');
        Schema::dropIfExists('battrei_team');
    }
};
