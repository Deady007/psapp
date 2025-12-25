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
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('acceptance_criteria')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_board_id', 'project_board_column_id']);
            $table->index('assignee_id');
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
