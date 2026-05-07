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
        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('applicant_id');
            $table->date('offer_date');
            $table->date('joining_date')->nullable();
            $table->string('offer_file_path')->nullable();
            $table->enum('status', Enum::keys('OFFER_LETTER_STATUS'))->default(Enum::keys('OFFER_LETTER_STATUS')[2]);
            $table->timestamps(); // created_at and updated_at

            $table->foreign('applicant_id')->references('id')->on('applicants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_letters');
    }
};
