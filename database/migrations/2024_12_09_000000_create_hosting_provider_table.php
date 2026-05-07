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
    public function up(): void
    {
        Schema::create('hosting_provider', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('provider_id'); // BIGINT AUTO_INCREMENT PRIMARY KEY
            $table->string('provider_name', 255); // VARCHAR(255) NOT NULL
            $table->timestamps(); // Includes 'created_at' and 'updated_at'
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
        Schema::dropIfExists('hosting_provider');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
