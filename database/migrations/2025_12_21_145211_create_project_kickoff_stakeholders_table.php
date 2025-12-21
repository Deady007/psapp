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
        Schema::create('project_kickoff_stakeholders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_kickoff_id')->constrained()->cascadeOnDelete();
            $table->string('stakeholder_type');
            $table->unsignedBigInteger('stakeholder_id');
            $table->timestamps();

            $table->index(['stakeholder_type', 'stakeholder_id'], 'kickoff_stakeholder_lookup');
            $table->unique(['project_kickoff_id', 'stakeholder_type', 'stakeholder_id'], 'kickoff_stakeholder_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_kickoff_stakeholders');
    }
};
