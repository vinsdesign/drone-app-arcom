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
        Schema::create('connection_cards', function (Blueprint $table) {
            $table->id();
            $table->string('slot');
            $table->string('type');
            $table->foreignId('remot_id')->constrainedTo('drone_connection')->cascadeDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connection_cards');
    }
};
