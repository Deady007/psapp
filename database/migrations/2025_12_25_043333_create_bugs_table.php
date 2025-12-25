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
        Schema::create('bugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_board_id')->constrained('project_boards')->cascadeOnDelete();
            $table->foreignId('story_id')->constrained()->cascadeOnDelete();
            $table->foreignId('testing_card_id')->nullable()->constrained('testing_cards')->nullOnDelete();
            $table->string('issue_key')->nullable();
            $table->unsignedInteger('issue_number')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity')->default('Medium');
            $table->text('steps_to_reproduce')->nullable();
            $table->string('status')->default('Open');
            $table->timestamp('found_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['project_board_id', 'status']);
            $table->unique('issue_key');
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bugs');
    }
};
