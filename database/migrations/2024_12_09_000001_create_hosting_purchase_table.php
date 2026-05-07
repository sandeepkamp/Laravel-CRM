<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hosting_purchase', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('hosting_provider_id');
            $table->text('plan_details'); // Plan details
            $table->date('activation_date'); // Activation date
            $table->date('date_of_purchase'); // Date of purchase
            $table->date('expiry_date'); // Expiry date
            $table->decimal('cost', 10, 2); // Cost
            $table->integer('status')->default(0); // Integer for status: 0 - activate plan, 1 - active, 2 - renew
            $table->timestamps();

            // Foreign keys
            $table->foreign('client_id')
                  ->references('id')
                  ->on('deals')
                  ->onDelete('cascade');

            $table->foreign('hosting_provider_id')
                  ->references('provider_id')
                  ->on('hosting_provider')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('hosting_purchase');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
