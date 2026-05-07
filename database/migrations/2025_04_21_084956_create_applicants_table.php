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
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_posting_id');
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email');
            $table->text('address')->nullable();
            $table->string('resume_path')->nullable();
            $table->enum('status', Enum::keys('APPLICANT_STATUS'))->default(Enum::keys('APPLICANT_STATUS')[0]);
            $table->timestamps(); // created_at and updated_at

            $table->foreign('job_posting_id')->references('id')->on('job_postings')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};
