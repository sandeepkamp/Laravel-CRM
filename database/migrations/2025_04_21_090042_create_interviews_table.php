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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->unsignedBigInteger('interviewer_id')->nullable();
            $table->dateTime('scheduled_date');
            $table->text('feedback')->nullable();
            $table->enum('status', Enum::keys('INTERVIEW_STATUS'))->default(Enum::keys('INTERVIEW_STATUS')[0]);
            $table->timestamps(); // created_at and updated_at

            $table->foreign('applicant_id')->references('id')->on('applicants')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('interviewer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
