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
		Schema::create("residence_addresses", function (Blueprint $table) {
			$table->id();
			$table->string("country");
			$table->string("commune")->nullable();
			$table->string("town")->nullable();
			$table->string("postal_code")->nullable();
			$table->string("street")->nullable();
			$table->string("house_number")->nullable();
			$table->string("flat_number")->nullable();
			$table->timestamps();
		});

		Schema::table("people", function (Blueprint $table) {
			$table->foreignId("residence_address_id")->nullable()->constrained("residence_addresses")->cascadeOnDelete();
		});

		Schema::table("guardians", function (Blueprint $table) {
			$table->foreignId("residence_address_id")->nullable()->constrained("residence_addresses")->cascadeOnDelete();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::table("people", function (Blueprint $table) {
			$table->dropForeign(["residence_address_id"]);
		});
		Schema::table("guardians", function (Blueprint $table) {
			$table->dropForeign(["residence_address_id"]);
		});
		Schema::dropIfExists("residence_addresses");
	}
};
