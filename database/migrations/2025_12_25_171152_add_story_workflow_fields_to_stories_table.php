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
        Schema::table('stories', function (Blueprint $table) {
            $table->string('issue_key')->nullable()->after('project_board_column_id');
            $table->unsignedInteger('issue_number')->nullable()->after('issue_key');
            $table->string('priority')->default('Medium')->after('title');
            $table->date('due_date')->nullable()->after('priority');
            $table->json('labels')->nullable()->after('notes');
            $table->unsignedInteger('estimate')->nullable()->after('labels');
            $table->string('estimate_unit')->nullable()->after('estimate');
            $table->json('reference_links')->nullable()->after('estimate_unit');
            $table->json('database_changes')->nullable()->after('reference_links');
            $table->boolean('database_changes_confirmed')->default(false)->after('database_changes');
            $table->json('page_mappings')->nullable()->after('database_changes_confirmed');
            $table->boolean('page_mappings_confirmed')->default(false)->after('page_mappings');
            $table->string('blocker_reason')->nullable()->after('page_mappings_confirmed');

            $table->unique('issue_key');
            $table->index(['priority', 'due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropUnique(['issue_key']);
            $table->dropIndex(['priority', 'due_date']);
            $table->dropColumn([
                'issue_key',
                'issue_number',
                'priority',
                'due_date',
                'labels',
                'estimate',
                'estimate_unit',
                'reference_links',
                'database_changes',
                'database_changes_confirmed',
                'page_mappings',
                'page_mappings_confirmed',
                'blocker_reason',
            ]);
        });
    }
};
