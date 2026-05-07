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
        Schema::create('customer_hostings', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('domain_name');
            $table->string('hosting_plan');
            // $table->unsignedBigInteger('hosting_feature_id');
            // $table->foreign('hosting_feature_id')->references('id')->on('hosting_features')->onUpdate('cascade')->onDelete('set null');
            $table->date('start_date');
            $table->date('expiry_date');
            $table->enum('status', Enum::keys('HOSTING_STATUSES'))->default(Enum::keys('HOSTING_STATUSES')[0]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_hostings');
    }
};
