<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('id')->constrained()->cascadeOnDelete();

            $table->index('project_id');
        });

        DB::table('documents')
            ->join('document_folders', 'documents.folder_id', '=', 'document_folders.id')
            ->whereNull('documents.project_id')
            ->update([
                'documents.project_id' => DB::raw('document_folders.project_id'),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropConstrainedForeignId('project_id');
        });
    }
};
