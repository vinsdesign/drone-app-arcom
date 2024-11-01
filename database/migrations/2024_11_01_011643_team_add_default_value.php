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
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('id_customers')->nullable()->after('id');
            $table->foreign('id_customers')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('set null');
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedBigInteger('id_projects')->nullable()->after('id_customers');
            $table->foreign('id_projects')
                  ->references('id')
                  ->on('projects')
                  ->onDelete('set null');
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->string('flight_type')->nullable()->after('id_projects');
        });
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('set_pilot')->nullable()->default(0)->after('flight_type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropForeign(['id_customer']);
            $table->dropColumn('id_customer');
            $table->dropForeign(['id_customers']);
            $table->dropColumn('id_customers');
            $table->dropForeign(['id_projects']);
            $table->dropColumn('id_projects');
            $table->dropColumn('flight_type');
            $table->dropColumn('set_pilot');
        });
    }
};
