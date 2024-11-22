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
        Schema::table('flighs', function (Blueprint $table) {
            $table->foreignId('location_id')->nullable()->references('id')->on('fligh_locations')->onDelete('set null')->default(null);
            $table->foreignId('projects_id')->nullable()->constrained('projects')->onDelete('set null');
        });
        Schema::table('fligh_locations', function (Blueprint $table) {
            $table->foreignId('projects_id')->nullable()->constrained('projects')->onDelete('set null');
        });
        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignId('projects_id')->nullable()->constrained('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('flighs', function (Blueprint $table) {
            $table->dropColumn('location_id');
            $table->dropColumn('projects_id');
        });
        Schema::table('fligh_locations', function (Blueprint $table) {
            $table->dropColumn('projects_id');
        });
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('projects_id');
        });
    }
};
