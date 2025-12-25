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
        Schema::create('testing_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_board_id')->constrained('project_boards')->cascadeOnDelete();
            $table->foreignId('project_board_column_id')->constrained('project_board_columns')->cascadeOnDelete();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tester_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('result')->nullable();
            $table->timestamp('tested_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique('story_id');
            $table->index(['project_board_id', 'project_board_column_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testing_cards');
    }
};
