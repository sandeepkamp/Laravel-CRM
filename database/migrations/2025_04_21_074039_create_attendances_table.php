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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('employee_code');
            $table->string('employee_name');
            $table->date('date');
            $table->time('punch_in');
            $table->time('punch_out')->nullable();
            
            $table->time('lunch_start')->nullable();
            $table->time('lunch_end')->nullable();
            $table->time('break_start')->nullable();
            $table->time('break_end')->nullable();

            $table->timestamps(); // includes created_at and updated_at

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
