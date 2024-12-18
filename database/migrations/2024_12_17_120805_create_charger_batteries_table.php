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
        Schema::create('charger_batteries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('duration');
            $table->text('note')->nullable();
            $table->integer('pre_flight')->nullable(); 
            $table->integer('post_flight')->nullable();
            $table->integer('before_charger')->nullable();
            $table->integer('after_charger')->nullable(); 
            $table->integer('capacity')->nullable();
            $table->integer('resistance')->nullable();
            $table->integer('cell1')->nullable(); 
            $table->integer('cell2')->nullable(); 
            $table->integer('cell3')->nullable(); 
            $table->integer('cell4')->nullable(); 
            $table->integer('cell5')->nullable(); 
            $table->integer('cell6')->nullable(); 
            $table->integer('cell7')->nullable(); 
            $table->integer('cell8')->nullable();
            $table->foreignId('batteris_id')->nullable()->constrained('battreis')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('charger_batteries');
    }
};
