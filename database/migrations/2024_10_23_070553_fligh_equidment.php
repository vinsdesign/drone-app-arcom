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
        Schema::create('fligh_equidment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fligh_id')->constrained('flighs')->cascadeOnDelete();
            $table->foreignId('equidment_id')->constrained('equidments')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fligh_equidment');
    }
};
