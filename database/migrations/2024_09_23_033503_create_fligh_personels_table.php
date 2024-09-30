<?php

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
        Schema::create('fligh_personels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pilot_id')->constrained('users')->cascadeDelete();
            $table->string('support_crew');
            $table->foreignId('instructor_id')->nullable()->constrained('users')->cascadeDelete();
            $table->foreignId('other_id')->nullable()->constrained('users')->cascadeDelete();
            $table->boolean('singgle')->default(false);
            $table->string('note');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fligh_personels');
    }
};
