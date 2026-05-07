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

        Schema::create('deals', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            $table->string('first_name', 255);

            $table->string('last_name', 255);

            $table->string('email', 255)->nullable();

            $table->string('phone', 255)->nullable();

            $table->string('title', 255)->nullable();

            $table->unsignedBigInteger('organisation')->nullable();

            $table->foreign('organisation')->references('id')->on('organisations')->onUpdate('cascade')->onDelete('cascade');

            $table->string('website')->nullable();

            $table->decimal('value', 11, 2)->nullable()->default('0.00');

            $table->unsignedBigInteger('currency')->nullable();

            $table->foreign('currency')->references('id')->on('currency')->onUpdate('cascade')->onDelete('set null');

            $table->date('close_date')->nullable();

            $table->unsignedBigInteger('owner')->nullable();

            $table->foreign('owner')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

            $table->unsignedBigInteger('visible_to')->nullable();

            $table->foreign('visible_to')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');

            $table->boolean('converted')->comment('1=>yes, 0=>no')->default(0);

            $table->timestamps();

        });

    }



    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('deals');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

};

