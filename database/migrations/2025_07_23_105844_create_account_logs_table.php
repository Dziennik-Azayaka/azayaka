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
		Schema::create("account_logs", function (Blueprint $table) {
			$table->id();
			$table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
			$table->enum("event_type", [
				"failed_login_attempt",
				"successful_login_attempt",
				"logout",
				"logged_out_by_another_device",
				"credentials_changed"
			]);
			$table->string("ip")->nullable();
			$table->string("user_agent")->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("account_logs");
	}
};
