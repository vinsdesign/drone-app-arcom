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
        Schema::create('wheaters', function (Blueprint $table) {
            $table->id();
            $table->float('temperature'); 
            $table->float('humidity'); 
            $table->string('note');
            $table->float('wind_speed')->nullable();
            $table->float('pressure')->nullable(); 
            $table->float('cloud_cover')->nullable(); 
            $table->float('visibility')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheaters');
    }
};
