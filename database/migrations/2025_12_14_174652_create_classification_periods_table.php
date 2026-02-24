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
			$table->foreignId("school_unit_id")->constrained("school_units");
			$table->integer("school_year");
			$table->integer("period_number");
			$table->date("period_start");
			$table->date("period_end");
            $table->timestamps();
        });

		Schema::create("class_units_periods", function (Blueprint $table) {
			$table->id();
			$table->foreignId("class_unit_id")->constrained("class_units")->onDelete("cascade");
			$table->foreignId("classification_period_id")->constrained("classification_periods")->onDelete("cascade");
			$table->integer("level");
			$table->unique(["class_unit_id", "classification_period_id"], "class_unit_periods_level_unique");
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
