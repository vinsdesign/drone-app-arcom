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
        Schema::create('equidment_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kits_id')->constrainedTo('kits')->cascadeDelete();
            $table->foreignId('equidment_id')->constrainedTo('equidment')->cascadeDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equidment_kits');
    }
};
