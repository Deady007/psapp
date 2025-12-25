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
        Schema::create('board_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_board_id')->constrained('project_boards')->cascadeOnDelete();
            $table->string('type');
            $table->longText('content');
            $table->string('storage_path');
            $table->string('file_name');
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->index(['project_board_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_documents');
    }
};
