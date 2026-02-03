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
		Schema::create("children_registries", function (Blueprint $table) {
			$table->id();
			$table->foreignId("school_unit_id");
			$table->timestamps();
		});

		Schema::create("children_registry_student", function (Blueprint $table) {
			$table->id();
			$table->foreignId("children_registry_id")->constrained("children_registries")->onDelete("cascade");
			$table->foreignId("student_id")->constrained("students")->onDelete("cascade");
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("children_registries");
	}
};
