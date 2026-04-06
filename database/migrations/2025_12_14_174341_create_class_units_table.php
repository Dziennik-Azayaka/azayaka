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
        Schema::create("class_units", function (Blueprint $table) {
            $table->id();
			$table->foreignId("school_unit_id")->constrained("school_units");
			$table->string("alias")->nullable();
			$table->string("mark");
			$table->foreignId("starting_classification_period_id");
			$table->integer("teaching_cycle_length");
			$table->string("promote_every")->default("year");
            $table->timestamps();
        });

		// form tutors
		Schema::create("class_units_form_tutors", function (Blueprint $table) {
			$table->id();
			$table->foreignId("class_unit_id")->constrained("class_units")->onDelete("cascade");
			$table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
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
        Schema::dropIfExists("class_units");
		Schema::dropIfExists("class_units_employees");
    }
};
