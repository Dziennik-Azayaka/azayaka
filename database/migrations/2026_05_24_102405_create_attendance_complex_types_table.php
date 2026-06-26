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
		Schema::create("attendance_complex_types", function (Blueprint $table) {
			$table->id();
			$table->string("name")->unique();
			$table->string("shortcut", 3)->unique();
			$table->integer("maps_to_primitive_type");
			$table->boolean("active")->default(true);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("attendance_complex_types");
	}
};
