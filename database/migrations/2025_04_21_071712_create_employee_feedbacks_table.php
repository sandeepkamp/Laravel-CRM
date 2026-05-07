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
        Schema::create('employee_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // Feedback receiver
            $table->unsignedBigInteger('submitted_by'); // Feedback giver (manager)
            $table->text('feedback_text');
            $table->decimal('rating', 3, 1)->nullable(); // Optional rating
            $table->enum('quarter', Enum::keys('QUARTER'));
            $table->timestamp('submitted_at')->useCurrent();
            $table->timestamp('update_at')->nullable();
            $table->timestamps(); // created_at and updated_at

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_feedback');
    }
};
