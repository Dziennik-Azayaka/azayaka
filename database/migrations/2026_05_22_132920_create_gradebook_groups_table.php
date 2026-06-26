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
		Schema::create("gradebook_groups", function (Blueprint $table) {
			$table->id();
			$table->foreignId("gradebook_id")->constrained("gradebooks")->onDelete("cascade");
			$table->string("name");
			$table->string("shortcut");
			$table->timestamps();

			$table->unique(["gradebook_id", "name"]);
			$table->unique(["gradebook_id", "shortcut"]);
		});

		Schema::create("gradebook_group_student", function (Blueprint $table) {
			$table->id();
			$table->foreignId("gradebook_group_id")->constrained("gradebook_groups")->onDelete("cascade");
			$table->foreignId("student_id")->constrained("students")->onDelete("cascade");
			$table->timestamps();

			$table->unique(["gradebook_group_id", "student_id"]);
		});

		Schema::create("gradebook_group_subjects", function (Blueprint $table) {
			$table->id();
			$table->foreignId("gradebook_id")->constrained("gradebooks")->onDelete("cascade");
			$table->foreignId("gradebook_group_id")->nullable()->constrained("gradebook_groups")->onDelete("cascade");
			$table->foreignId("subject_id")->constrained("subjects")->onDelete("cascade");
			$table->string("description");
			$table->timestamps();

			$table->unsignedBigInteger("gradebook_group_id_key")
				->storedAs("COALESCE(gradebook_group_id, 0)");
			$table->unique(
				["gradebook_id", "gradebook_group_id_key", "subject_id"],
				"gradebook_subject_unique"
			);
		});

		Schema::create("employee_gradebook_group_subject", function (Blueprint $table) {
			$table->id();

			// TIL: Maria/MySQL has a 64-char limit for indexes
			$table->unsignedBigInteger("gradebook_group_subject_id");
			$table->foreign("gradebook_group_subject_id", "empl_grad_group_sub_subjects")
				->references("id")
				->on("gradebook_group_subjects")
				->onDelete("cascade");

			$table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
			$table->timestamps();

			$table->unique(["gradebook_group_subject_id", "employee_id"], "employee_gradebook_group_subject_unique");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("employee_gradebook_group_subject");
		Schema::dropIfExists("gradebook_group_student");
		Schema::dropIfExists("gradebook_group_subjects");
		Schema::dropIfExists("gradebook_groups");
	}
};
