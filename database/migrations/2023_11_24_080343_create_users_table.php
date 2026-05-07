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

        Schema::create('users', function (Blueprint $table) {

            $table->engine = 'InnoDB';

            $table->bigIncrements('id');

            $table->string('first_name', 255);

            $table->string('last_name', 255)->nullable();

            $table->string('email')->unique();

            $table->string('phone')->nullable();

            $table->string('password');

            $table->date('date_registered');

            $table->integer('status')->comment('1=>active, 2=>inactive, 3=>blocked');

            $table->unsignedBigInteger('role')->nullable();

            $table->foreign('role')->references('id')->on('user_role')->onUpdate('cascade')->onDelete('set null');

            $table->rememberToken();

            $table->timestamps();

        });

    }



    /**

     * Reverse the migrations.

     */

    public function down(): void

    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Schema::dropIfExists('users');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

};

