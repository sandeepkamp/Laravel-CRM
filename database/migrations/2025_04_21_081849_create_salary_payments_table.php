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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->year('year');
            $table->unsignedTinyInteger('month'); // 1 to 12
            $table->decimal('gross_salary', 10, 2)->default(0);
            $table->enum('payment_mode', Enum::keys('PAYMENT_MODE'))->default(Enum::keys('PAYMENT_MODE')[0]);
            $table->timestamps(); // created_at and updated_at

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
