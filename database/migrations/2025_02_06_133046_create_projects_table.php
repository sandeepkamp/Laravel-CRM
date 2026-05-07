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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->text('project_name');
            $table->longText('description');
            $table->integer('project_hours')->default(0);
            $table->integer('status')->comment('0=>Not Started, 1=>Initiation Stage, 2=>Planning Stage, 3=>Execution Stage, 4=>Monitoring & Controlling Stage, 5=>Testing Phase, 6=>Closure Stage, 7=>Completed, 8=>Hannded Over to Client');

            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null')->onUpdate('cascade');
            
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();

            // change users table columns
            Schema::table('users', function (Blueprint $table) {
                $table->string('emp_id')->nullable()->after('id');
            });
            Schema::table('user_role', function (Blueprint $table) {
                $table->string('department')->nullable()->after('name');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
