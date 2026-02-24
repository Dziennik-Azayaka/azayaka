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
			$table->string("alias");
			$table->string("mark");
			$table->integer("starting_school_year");
			$table->integer("teaching_cycle_length");
			$table->string("promote_every")->default("year");
            $table->timestamps();
        });

		// form teachers
		Schema::create("class_units_employees", function (Blueprint $table) {
			$table->id();
			$table->foreignId("class_unit_id")->constrained("class_units")->onDelete("cascade");
			$table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
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
