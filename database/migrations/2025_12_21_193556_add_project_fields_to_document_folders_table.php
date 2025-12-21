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
        Schema::table('document_folders', function (Blueprint $table) {
            $table->foreignId('project_id')->after('id')->constrained()->cascadeOnDelete();
            $table->string('kind')->default('folder')->after('project_id');

            $table->index(['project_id', 'kind']);
            $table->index(['project_id', 'parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_folders', function (Blueprint $table) {
            $table->dropIndex(['project_id', 'parent_id']);
            $table->dropIndex(['project_id', 'kind']);
            $table->dropConstrainedForeignId('project_id');
            $table->dropColumn('kind');
        });
    }
};
