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
            $table->string('site_location')->nullable()->after('meeting_mode');
            $table->string('meeting_link')->nullable()->after('site_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_kickoffs', function (Blueprint $table) {
            $table->dropColumn(['site_location', 'meeting_link']);
        });
    }
};
