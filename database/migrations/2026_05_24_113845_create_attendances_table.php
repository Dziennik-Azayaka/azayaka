<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		Schema::create("attendances", function (Blueprint $table) {
			$table->id();
			$table->foreignId("student_id")->constrained("students")->cascadeOnDelete();
			$table->foreignId("lesson_id")->constrained("lessons")->cascadeOnDelete();
			$table->foreignId("attendance_complex_type_id")
				->constrained("attendance_complex_types")
				->cascadeOnDelete();
			// update every time the attendance is changed for an accurate edit history
			$table->foreignId("employee_id")->nullable()->constrained("employees")->cascadeOnDelete();
			$table->unique(["lesson_id", "student_id"]);
			$table->index("student_id");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("attendances");
	}
};
