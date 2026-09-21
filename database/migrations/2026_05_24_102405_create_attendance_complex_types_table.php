<?php

use App\Enums\AttendancePrimitiveType;
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
			$table->boolean("built_in")->default(false);
			$table->boolean("active")->default(true);
			$table->timestamps();
		});

		DB::table("attendance_complex_types")->insert([
			["name" => "Obecność", "shortcut" => "+", "maps_to_primitive_type" => AttendancePrimitiveType::PRESENCE, "built_in" => true],
			["name" => "Nieobecność nieusprawiedliwiona", "shortcut" => "-", "maps_to_primitive_type" => AttendancePrimitiveType::ABSENCE, "built_in" => true],
			["name" => "Nieobecność usprawiedliwiona", "shortcut" => "u", "maps_to_primitive_type" => AttendancePrimitiveType::EXCUSED_ABSENCE, "built_in" => true],
			["name" => "Spóźnienie", "shortcut" => "s", "maps_to_primitive_type" => AttendancePrimitiveType::LATENESS, "built_in" => true],
			["name" => "Spóźnienie usprawiedliwione", "shortcut" => "su", "maps_to_primitive_type" => AttendancePrimitiveType::EXCUSED_LATENESS, "built_in" => true],
			["name" => "Zwolnienie", "shortcut" => "z", "maps_to_primitive_type" => AttendancePrimitiveType::EXEMPTION, "built_in" => true],
		]);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists("attendance_complex_types");
	}
};
