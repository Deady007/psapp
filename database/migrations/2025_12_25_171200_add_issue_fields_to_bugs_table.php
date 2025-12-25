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
        Schema::table('bugs', function (Blueprint $table) {
            $table->string('issue_key')->nullable()->after('testing_card_id');
            $table->unsignedInteger('issue_number')->nullable()->after('issue_key');
            $table->string('severity')->default('Medium')->after('description');
            $table->text('steps_to_reproduce')->nullable()->after('severity');
            $table->timestamp('found_at')->nullable()->after('steps_to_reproduce');
            $table->timestamp('resolved_at')->nullable()->after('found_at');
            $table->timestamp('closed_at')->nullable()->after('resolved_at');

            $table->unique('issue_key');
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bugs', function (Blueprint $table) {
            $table->dropUnique(['issue_key']);
            $table->dropIndex(['severity']);
            $table->dropColumn([
                'issue_key',
                'issue_number',
                'severity',
                'steps_to_reproduce',
                'found_at',
                'resolved_at',
                'closed_at',
            ]);
        });
    }
};
