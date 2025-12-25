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
        Schema::create('project_board_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_board_id')->constrained('project_boards')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->unique(['project_board_id', 'name']);
            $table->index(['project_board_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_board_columns');
    }
};
