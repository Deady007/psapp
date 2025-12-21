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
            $table->dateTime('planned_at')->nullable()->after('purchase_order_number');
            $table->dateTime('completed_at')->nullable()->after('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_kickoffs', function (Blueprint $table) {
            $table->dropColumn(['planned_at', 'completed_at']);
        });
    }
};
