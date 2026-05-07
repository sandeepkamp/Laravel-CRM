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
        Schema::create('asset_handovers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('asset_name');

            $table->enum('handover_status', Enum::keys('ASSET_HANDOVER_STATUS'))
                ->default(Enum::keys('ASSET_HANDOVER_STATUS')[0]);

            $table->date('handover_date')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_handovers');
    }
};
