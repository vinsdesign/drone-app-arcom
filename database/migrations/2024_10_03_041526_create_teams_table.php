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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('owner')->nullable();
            $table->string('website')->nullable();
            $table->integer('company_size')->nullable();
            $table->string('gov_registration')->nullable();
            $table->string('legal_id')->nullable();
            $table->integer('exemption_number')->nullable();
            $table->string('category')->nullable();
            $table->string('address')->nullable();
            $table->string('state')->nullable();
            $table->foreignId('cities_id')->constrainedTo('cities')->nullable()->cascadeOnDelete('set null');
            $table->string('postal_code')->nullable();
            $table->foreignId('countries_id')->constrainedTo('countries')->nullable()->cascadeOnDelete('set null');
            $table->boolean('insurance')->nullable();
            $table->integer('insurance_amount')->nullable();
            $table->string('activity')->nullable();
            $table->string('note')->nullable();
            $table->foreignId('currencies_id')->nullable()->constrained('currencies')->onDelete('set null');
            $table->timestamps();
        });
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
        Schema::dropIfExists('team_user');
    }
};
