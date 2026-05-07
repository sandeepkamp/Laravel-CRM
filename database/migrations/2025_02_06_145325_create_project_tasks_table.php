<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Enum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->text('task_name');
            $table->longText('description')->nullable();
            $table->char('estimated_time', 255)->comment('in minutes')->default(0);
            $table->dateTime('due_date')->nullablbe();
            $table->enum('priority', Enum::getPriorities())->default('low');

            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade')->onUpdate('cascade');

            $table->unsignedBigInteger('milestone_id')->nullable();
            $table->foreign('milestone_id')->references('id')->on('milestones')->onDelete('set null')->onUpdate('cascade');

            $table->unsignedBigInteger('status')->nullable();
            $table->foreign('status')->references('id')->on('task_status')->onDelete('set null')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};
