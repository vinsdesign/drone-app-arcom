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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('refnumber');
            $table->date('expired_date');
            $table->string('scope');
            $table->string('external link');
            $table->string('description')->nullable();
            $table->string('doc')->nullable();
            $table->foreignId('users_id')->constrained('users')->cascadeOnDelete()->nullable();
            $table->foreignId('customers_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->foreignId('projects_id')->nullable()->constrained('projects')->cascadeOnDelete(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
