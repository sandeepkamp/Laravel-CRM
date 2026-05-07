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
        Schema::create('products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('id');
            $table->text('name');
            $table->decimal('price', 11, 2)->default(0);
            $table->integer('quantity')->default(0);
            $table->decimal('tax', 11, 2)->default(0)->comment('precent');
            $table->decimal('tax_amount', 11, 2)->default(0);
            $table->decimal('total_without_tax', 11, 2)->default(0);
            $table->decimal('total_with_tax', 11, 2)->default(0);
            $table->unsignedBigInteger('deal_id');
            $table->foreign('deal_id')->references('id')->on('deals')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('products');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
