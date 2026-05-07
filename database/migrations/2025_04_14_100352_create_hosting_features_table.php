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
        Schema::create('hosting_features', function (Blueprint $table) {
            $table->id();
            // $table->string('plan_name')->unique(); // e.g., "Basic", "Pro", "Enterprise"
            $table->enum('server_type', Enum::keys('SERVER_TYPES'))->default(Enum::keys('SERVER_TYPES')[0]);
            $table->integer('storage'); // in GB
            $table->integer('bandwidth'); // in GB
            $table->integer('no_of_emails');
            $table->enum('ssl', Enum::keys('YES_NO'))->default(Enum::keys('YES_NO')[1]);
            $table->enum('backup', Enum::keys('YES_NO'))->default(Enum::keys('YES_NO')[1]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hosting_features');
    }
};
