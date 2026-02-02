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
        Schema::create("residence_addresses", function (Blueprint $table) {
            $table->id();
			$table->string("country");
			$table->string("commune")->nullable();
			$table->string("town")->nullable();
			$table->string("postal_code")->nullable();
			$table->string("street")->nullable();
			$table->string("house_number")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("residence_addresses");
    }
};
