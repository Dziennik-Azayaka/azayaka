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
		});

		Schema::create("lessons_gradebooks", function (Blueprint $table) {
			$table->id();
			$table->foreignId("lesson_id")->constrained("lessons")->onDelete("cascade");
			$table->foreignId("gradebook_id")->constrained("gradebooks")->onDelete("cascade");
			$table->unique(["lesson_id", "gradebook_id"]);
			$table->timestamps();
		});

		Schema::create("lessons_assisting_teachers", function (Blueprint $table) {
			$table->id();
			$table->foreignId("lesson_id")->constrained("lessons")->onDelete("cascade");
			$table->foreignId("employee_id")->constrained("employees")->onDelete("cascade");
			$table->timestamps();
		});

		Schema::create("lessons_gradebook_group", function (Blueprint $table) {
			$table->id();
			$table->foreignId("lesson_id")->constrained("lessons")->onDelete("cascade");
			$table->foreignId("gradebook_group_id")->constrained("gradebook_groups")->onDelete("cascade");
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("lessons_gradebook_group");
		Schema::dropIfExists("lessons_assisting_teachers");
		Schema::dropIfExists("lessons_gradebooks");
		Schema::dropIfExists("lessons");
	}
};
