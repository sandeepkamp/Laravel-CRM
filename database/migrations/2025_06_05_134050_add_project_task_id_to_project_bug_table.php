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
        Schema::table('project_bug', function (Blueprint $table) {
            $table->unsignedBigInteger('project_task_id')->nullable()->after('milestone_id');

            $table->foreign('project_task_id')
                  ->references('id')
                  ->on('project_tasks')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_bug', function (Blueprint $table) {
            $table->dropForeign(['project_task_id']);
            $table->dropColumn('project_task_id');
        });
    }
};
