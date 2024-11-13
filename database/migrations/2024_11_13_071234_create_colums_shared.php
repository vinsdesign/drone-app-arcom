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
        Schema::table('projects', function (Blueprint $table) {
            $table->integer('shared')->nullable()->default(1);
        });
        Schema::table('documents', function (Blueprint $table) {
           $table->integer('shared')->nullable()->default(1);
        });
        Schema::table('fligh_locations', function (Blueprint $table) {
           $table->integer('shared')->nullable()->default(1);
        });
        Schema::table('flighs', function (Blueprint $table) {
            $table->integer('shared')->nullable()->default(1);
         });
         Schema::table('drones', function (Blueprint $table) {
            $table->integer('shared')->nullable()->default(1);
         });
         Schema::table('equidments', function (Blueprint $table) {
            $table->integer('shared')->nullable()->default(1);
         });
         Schema::table('battreis', function (Blueprint $table) {
            $table->integer('shared')->nullable()->default(1);
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('shared');
        });
        Schema::table('documents', function (Blueprint $table) {
           $table->dropColumn('shared');
        });
        Schema::table('fligh_locations', function (Blueprint $table) {
           $table->dropColumn('shared');
        });
        Schema::table('flighs', function (Blueprint $table) {
            $table->dropColumn('shared');
         });
         Schema::table('drones', function (Blueprint $table) {
            $table->dropColumn('shared');
         });
         Schema::table('equidments', function (Blueprint $table) {
            $table->dropColumn('shared');
         });
         Schema::table('battreis', function (Blueprint $table) {
            $table->dropColumn('shared');
         });
        
    }
};
