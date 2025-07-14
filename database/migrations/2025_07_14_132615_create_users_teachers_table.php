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
		Schema::create("users_teachers", function (Blueprint $table) {
			$table->id();
			$table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
			$table->foreignId("teacher_id")->constrained("teachers")->cascadeOnDelete();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("users_teachers");
	}
};
