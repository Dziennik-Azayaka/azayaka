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
        Schema::create("gradebooks", function (Blueprint $table) {
            $table->id();
			// starting classification period
			$table->foreignId("classification_period_id")->constrained("classification_periods");
			$table->foreignId("class_unit_id")->constrained("class_units");
            $table->timestamps();
        });

		Schema::create("gradebooks_students", function (Blueprint $table) {
			$table->id();
			$table->foreignId("gradebook_id")->constrained("gradebooks")->onDelete("cascade");
			$table->foreignId("student_id")->constrained("students")->onDelete("cascade");
			$table->date("date_from");
			$table->date("date_to");
			$table->timestamps();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
		Schema::dropIfExists("gradebooks");
		Schema::dropIfExists("gradebooks_students");
    }
};
