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
		Schema::create("students", function (Blueprint $table) {
			$table->id();
			$table->foreignId("person_id")->constrained("people")->cascadeOnDelete();
			$table->date("admission_date");
			$table->date("leave_date")->nullable();
			$table->string("leave_reason")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("students");
	}
};
