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
		Schema::create("lessons", function (Blueprint $table) {
			$table->id();
			$table->integer("number");
			$table->foreignId("primary_teacher_id")->constrained("employees")->onDelete("cascade");
			$table->foreignId("subject_id")->constrained("subjects")->onDelete("cascade");
			$table->string("topic");
			$table->date("date");
			$table->time("start_time");
			$table->time("end_time");
			$table->boolean("completed")->default(false);
			$table->timestamps();
			$table->softDeletes();
			$table->index(["primary_teacher_id"]);
			$table->index(["subject_id"]);
		});

		Schema::create("lesson_gradebooks", function (Blueprint $table) {
			$table->id();
			$table->foreignId("lesson_id")->constrained("lessons")->onDelete("cascade");
			$table->foreignId("gradebook_id")->constrained("gradebooks")->onDelete("cascade");
			$table->unique(["lesson_id", "gradebook_id"]);
			$table->index(["gradebook_id", "lesson_id"]);
			$table->timestamps();
		});

		Schema::create("lessons_assisting_teachers", function (Blueprint $table) {
			$table->id();
			$table->foreignId("lesson_id")->constrained("lessons")->onDelete("cascade");
			$table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
			$table->unique(["lesson_id", "employee_id"]);
			$table->index(["employee_id", "lesson_id"]);
			$table->timestamps();
		});

		Schema::create("lesson_gradebook_groups", function (Blueprint $table) {
			$table->id();
			$table->foreignId("lesson_gradebook_id")->constrained("lesson_gradebooks")->onDelete("cascade");
			$table->foreignId("gradebook_group_id")->constrained("gradebook_groups")->onDelete("cascade");
			$table->unique(["lesson_gradebook_id", "gradebook_group_id"], "lesson_gradebook_groups_unique");
			$table->index(["gradebook_group_id", "lesson_gradebook_id"], "lesson_gradebook_groups_index");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("lesson_gradebook_groups");
		Schema::dropIfExists("lessons_assisting_teachers");
		Schema::dropIfExists("lesson_gradebooks");
		Schema::dropIfExists("lessons");
	}
};
