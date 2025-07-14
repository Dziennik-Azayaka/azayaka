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
		Schema::create("activation_codes", function (Blueprint $table) {
			$table->id();
			$table->string("words");
			$table->foreignId("student_id")->nullable()->constrained("students")->cascadeOnDelete();
			$table->enum("acts_as", ["student", "guardian"])->nullable();
			$table->foreignId("teacher_id")->nullable()->constrained("teachers")->cascadeOnDelete();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("activation_codes");
	}
};
