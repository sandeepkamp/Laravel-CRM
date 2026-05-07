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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->longText('description');
            $table->unsignedBigInteger('department_id');
            $table->string('location');
            $table->date('posted_date');
            $table->enum('status', Enum::keys('JOB_POSTING_STATUS'))->default(Enum::keys('JOB_POSTING_STATUS')[0]);
            $table->timestamps(); // created_at and updated_at

            $table->foreign('department_id')->references('id')->on('departments')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
