<?php
use App\Models\team;
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
            $table->foreignIdFor(Team::class,'teams_id')->index();
            $table->foreignId('users_id')->constrained('users')->cascadeOnDelete()->nullable();
            $table->foreignId('customers_id')->nullable()->constrained('customers')->cascadeOnDelete();
            $table->foreignId('projects_id')->nullable()->constrained('projects')->cascadeOnDelete(); 
            $table->timestamps();
        });
        Schema::create('document_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrainedTo('teams')->cascadeOnDelete();
            $table->foreignId('document_id')->constrained('documents')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_team');
    }
};
