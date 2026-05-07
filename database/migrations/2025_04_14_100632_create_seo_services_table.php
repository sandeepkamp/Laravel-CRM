<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\Enum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('seo_services', function (Blueprint $table) {
            $table->id();
            // $table->string('customer_name');
            $table->unsignedBigInteger('client_id');
            $table->string('website_url');
            $table->string('industry');
            $table->enum('seo_package', Enum::keys('SEO_PACKAGES'))->default(Enum::keys('SEO_PACKAGES')[0]);
            $table->date('start_date');
            $table->date('renewal_date');
            $table->enum('status', Enum::keys('SEO_STATUSES'))->default(Enum::keys('SEO_STATUSES')[2]);
            $table->timestamps();
            
            $table->foreign('client_id')->references('id')->on('deals')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_services');
    }
};
