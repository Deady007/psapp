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
        Schema::table('story_status_histories', function (Blueprint $table) {
            $table->timestamp('moved_at')->nullable()->after('moved_by');
            $table->string('reason')->nullable()->after('moved_at');
            $table->text('notes')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('story_status_histories', function (Blueprint $table) {
            $table->dropColumn(['moved_at', 'reason', 'notes']);
        });
    }
};
