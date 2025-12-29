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
        Schema::create("classification_period_defaults", function (Blueprint $table) {
            $table->id();
			$table->foreignId("school_unit_id")->constrained("school_units");
			$table->integer("school_year");
			$table->integer("period_number");
			$table->date("period_start");
			$table->date("period_end");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("classification_period_defaults");
    }
};
