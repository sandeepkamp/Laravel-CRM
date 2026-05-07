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

        Schema::create('deals_stages', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            $table->unsignedBigInteger('deal_id');

            $table->foreign('deal_id')->references('id')->on('deals')->onUpdate('cascade')->onDelete('cascade');

            $table->unsignedBigInteger('stage')->nullable();

            $table->foreign('stage')->references('id')->on('deals_stages_list')->onUpdate('cascade')->onDelete('set null');

            $table->timestamps();

        });

    }



    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('deals_stages');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

};

