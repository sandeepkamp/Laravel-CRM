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
        Schema::create('attendance_regularizations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('attendance_date');
            $table->text('reason');
            $table->enum('status', Enum::keys('ATTENDANCE_REGULARIZATIONS'))->default(Enum::keys('ATTENDANCE_REGULARIZATIONS')[0]);
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamps(); // created_at and updated_at

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_regularizations');
    }
};
