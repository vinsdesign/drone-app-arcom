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
        Schema::create('maintence_eqs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->string('status');
            $table->integer('cost');
            $table->foreignId('currencies_id')->constrained('currencies')->cascadeDelete();
            $table->string('notes');
            $table->foreignId('equidment_id')->constrainedTo('equidment')->cascadeDelete();
            $table->foreignId('battrei_id')->constrainedTo('battrei')->cascadeDelete();
            $table->foreignIdFor(Team::class,'teams_id')->index()->cascadeOnDelete();
            $table->timestamps();
        });
        Schema::create('maintence_eq_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('maintence_eq_id')->constrained('maintence_eqs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintence_eqs');
        Schema::dropIfExists('maintence_eq_team');
    }
};
