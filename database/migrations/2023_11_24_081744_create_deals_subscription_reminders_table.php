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
        Schema::create('deals_subscription_reminders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subscription_id');
            $table->foreign('subscription_id')->references('id')->on('deals_subscriptions')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('reminder_at');
            $table->boolean('status')->comment('0=>reminder pending, 1=> reminder done')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('deals_subscription_reminders');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
