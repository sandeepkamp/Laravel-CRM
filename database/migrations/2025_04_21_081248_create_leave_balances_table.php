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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->year('year');
            $table->integer('opening_balance')->default(0);
            $table->integer('used_days')->default(0);
            $table->integer('closing_balance')->default(0);
            $table->timestamps(); // created_at and updated_at

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
