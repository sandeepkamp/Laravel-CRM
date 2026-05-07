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
        Schema::create('appraisals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->enum('period', Enum::keys('PERIOD'))->default(Enum::keys('PERIOD')[2]);
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->decimal('rating', 4, 2)->nullable(); // Example: 4.50
            $table->longText('comments')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->timestamps(); // created_at and updated_at

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appraisals');
    }
};
