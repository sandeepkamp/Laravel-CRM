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
        Schema::create('deals_activity', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('deal_id');
            $table->foreign('deal_id')->references('id')->on('deals')->onUpdate('cascade')->onDelete('cascade');
            $table->integer('type')->comment('1=>call, 2=>meeting, 3=>task, 4=>deadline, 5=>email, 6=>note, 7=>files');
            $table->string('title', 255)->nullable();
            $table->longText('description')->nullable();
            $table->dateTime('time_from')->nullable();
            $table->dateTime('time_to')->nullable();
            $table->boolean('done')->default(0)->comment('0=>not done, 1=>done');
            $table->integer('status')->default(0)->comment('0=>reminder incomplete, 2=>reminder complete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('deals_activity');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
