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

		Schema::table("students", function (Blueprint $table) {
			$table->foreignId("children_registry_id")->nullable()->constrained("children_registries")->cascadeOnDelete();
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
