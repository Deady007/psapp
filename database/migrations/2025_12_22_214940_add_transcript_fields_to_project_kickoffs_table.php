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
        Schema::table('project_kickoffs', function (Blueprint $table) {
            $table->string('transcript_path')->nullable()->after('notes');
            $table->timestamp('transcript_uploaded_at')->nullable()->after('transcript_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_kickoffs', function (Blueprint $table) {
            $table->dropColumn(['transcript_path', 'transcript_uploaded_at']);
        });
    }
};
