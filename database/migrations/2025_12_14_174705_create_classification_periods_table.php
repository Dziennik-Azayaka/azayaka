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
        Schema::create("classification_periods", function (Blueprint $table) {
            $table->id();
			$table->foreignId("class_unit_id")->constrained("class_units");
			$table->integer("school_year");
			$table->integer("teaching_cycle_length_id");
			$table->string("mark");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("classification_periods");
    }
};
