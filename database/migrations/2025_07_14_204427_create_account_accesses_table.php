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
		Schema::create("account_accesses", function (Blueprint $table) {
			$table->id();
			$table->string("words")->nullable();
			$table->foreignId("student_id")->nullable()->constrained("students")->cascadeOnDelete();
			$table->foreignId("guardian_id")->nullable()->constrained("guardians")->cascadeOnDelete();
			$table->foreignId("employee_id")->nullable()->constrained("employees")->cascadeOnDelete();
			$table->foreignId("user_id")->nullable()->constrained("users")->cascadeOnDelete();
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
