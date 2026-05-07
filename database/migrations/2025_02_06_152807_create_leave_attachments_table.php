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
        Schema::create('leave_attachments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('leave_id');
            $table->foreign('leave_id')->references('id')->on('leaves')->onDelete('cascade')->onUpdate('cascade');

            $table->string('file_name');
            $table->char('file_type', 100);
            $table->char('file_size', 100)->comment('in bytes');

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_attachments');
    }
};
