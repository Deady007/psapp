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
        Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_board_id')->constrained('project_boards')->cascadeOnDelete();
            $table->foreignId('project_board_column_id')->constrained('project_board_columns')->cascadeOnDelete();
            $table->string('issue_key')->nullable();
            $table->unsignedInteger('issue_number')->nullable();
            $table->string('title');
            $table->string('priority')->default('Medium');
            $table->date('due_date')->nullable();
            $table->text('description')->nullable();
            $table->text('acceptance_criteria')->nullable();
            $table->text('notes')->nullable();
            $table->json('labels')->nullable();
            $table->unsignedInteger('estimate')->nullable();
            $table->string('estimate_unit')->nullable();
            $table->json('reference_links')->nullable();
            $table->json('database_changes')->nullable();
            $table->boolean('database_changes_confirmed')->default(false);
            $table->json('page_mappings')->nullable();
            $table->boolean('page_mappings_confirmed')->default(false);
            $table->string('blocker_reason')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_board_id', 'project_board_column_id']);
            $table->unique('issue_key');
            $table->index(['priority', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
