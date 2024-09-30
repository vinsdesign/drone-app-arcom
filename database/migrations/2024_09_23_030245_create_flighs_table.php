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
        Schema::create('flighs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('date');
            $table->time('duration');
            $table->string('type');
            $table->foreignId('customer_id')->constrainedTo('customer')->cascadeDelete();
            $table->foreignId('location_id')->constrainedTo('fligh_location')->cascadeDelete();
            $table->foreignId('project_id')->constrainedTo('project')->cascadeDelete();
            $table->foreignId('personel_id')->constrainedTo('fligh_personel')->cascadeDelete();
            $table->foreignId('eq_on_board_id')->constrainedTo('equidment_on_board')->cascadeDelete();
            $table->foreignId('wheater_id')->constrainedTo('wheater')->cascadeDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flighs');
    }
};
