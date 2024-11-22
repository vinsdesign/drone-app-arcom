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
            $table->string('model')->nullable();
            $table->string('status')->nullable();
            $table->string('inventory_asset')->nullable();
            $table->string('serial')->nullable();
            $table->string('type')->nullable();
            $table->foreignId('drones_id')->nullable()->constrained('drones')->onDelete('set null');
            $table->foreignId('users_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('purchase_date')->nullable();
            $table->integer('insurable_value')->nullable();
            $table->integer('weight')->nullable();
            $table->string('firmware_v')->nullable();
            $table->string('hardware_v')->nullable();
            $table->boolean('is_loaner')->nullable()->default(false)->nullable();
            $table->string('description')->nullable();
            $table->foreignIdFor(Team::class,'teams_id')->index();
            $table->timestamps();
        });

        Schema::create('equidment_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('equidment_id')->constrained('equidments')->cascadeOnDelete();
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
