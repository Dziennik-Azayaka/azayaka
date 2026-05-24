<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Models\AttendanceComplexType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class AttendanceComplexTypeControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_attendance_complex_types(): void
	{
		$this->actingAdminUser();

		$type = AttendanceComplexType::factory()->create();
		AttendanceComplexType::factory()->create([
			"name" => "Inny tytuł aby nie było konfliktów",
			"shortcut" => "INN",
			"active" => false
		]);

		$response = $this->getJson("/api/attendanceComplexTypes");

		$response->assertStatus(200);
		$response->assertJsonCount(2);
		$response->assertJsonPath("0.id", $type->id);
		$response->assertJsonPath("0.name", $type->name);
		$response->assertJsonPath("0.shortcut", $type->shortcut);
		$response->assertJsonPath("0.active", true);
		$response->assertJsonPath("0.primitiveType", $type->maps_to_primitive_type);
	}

	public function test_can_create_attendance_complex_type(): void
	{
		$this->actingAdminUser();

		$payload = [
			"name" => "Nieobecność z przyczyn szkolnych",
			"shortcut" => "NS",
			"mapsToPrimitiveType" => AttendancePrimitiveType::PRESENCE->value,
		];

		$response = $this->postJson("/api/attendanceComplexTypes", $payload);

		$response->assertStatus(201)
			->assertJson([
				"success" => true
			])
			->assertJsonStructure([
				"success",
				"id"
			]);

		$this->assertDatabaseHas("attendance_complex_types", [
			"name" => $payload["name"],
			"shortcut" => $payload["shortcut"],
			"maps_to_primitive_type" => $payload["mapsToPrimitiveType"],
			"active" => true
		]);
	}

	public function test_cannot_create_attendance_complex_type_with_invalid_data(): void
	{
		$this->actingAdminUser();

		$response = $this->postJson("/api/attendanceComplexTypes", [
			"name" => "Nieobecność z przyczyn udziału w olimpiadzie Scratcha",
			"shortcut" => "NPUOS",
			"mapsToPrimitiveType" => 999
		]);

		$response->assertStatus(422);
		$this->assertDatabaseCount("attendance_complex_types", 0);
	}

	public function test_can_update_attendance_complex_type(): void
	{
		$this->actingAdminUser();

		$type = AttendanceComplexType::factory()->create();

		$payload = [
			"name" => "Nieobecność Pro 5G",
			"shortcut" => "NP5",
			"mapsToPrimitiveType" => AttendancePrimitiveType::ABSENCE->value
		];

		$response = $this->putJson("/api/attendanceComplexTypes/$type->id", $payload);

		$response->assertStatus(200)
			->assertJson([
				"success" => true
			]);

		$this->assertDatabaseHas("attendance_complex_types", [
			"id" => $type->id,
			"name" => $payload["name"],
			"shortcut" => $payload["shortcut"],
			"maps_to_primitive_type" => AttendancePrimitiveType::ABSENCE->value,
			"active" => true
		]);
	}

	public function test_update_attendance_complex_type_requires_valid_data(): void
	{
		$this->actingAdminUser();

		$type = AttendanceComplexType::factory()->create();
		$response = $this->putJson("/api/attendanceComplexTypes/$type->id", [
			"name" => "Nieobecność z przyczyn udziału w olimpiadzie Scratcha",
			"shortcut" => "NPUOS",
			"mapsToPrimitiveType" => 999
		]);

		$response->assertStatus(422);
		$this->assertDatabaseHas("attendance_complex_types", [
			"id" => $type->id,
			"name" => $type->name,
			"shortcut" => $type->shortcut,
			"maps_to_primitive_type" => $type->maps_to_primitive_type,
			"active" => true
		]);
		$this->assertDatabaseCount("attendance_complex_types", 1);
		$this->assertDatabaseMissing("attendance_complex_types", [
			"name" => "Nieobecność z przyczyn udziału w olimpiadzie Scratcha",
			"shortcut" => "NPUOS",
			"maps_to_primitive_type" => 999
		]);
	}

	public function test_can_disable_active_attendance_complex_type(): void
	{
		$this->actingAdminUser();

		$type = AttendanceComplexType::factory()->create();
		$response = $this->patchJson("/api/attendanceComplexTypes/$type->id");

		$response->assertStatus(200)
			->assertJson([
				"success" => true
			]);

		$this->assertDatabaseHas("attendance_complex_types", [
			"id" => $type->id,
			"active" => false
		]);
	}

	public function test_can_enable_inactive_attendance_complex_type(): void
	{
		$this->actingAdminUser();

		$type = AttendanceComplexType::factory()->create([
			"active" => false
		]);
		$response = $this->patchJson("/api/attendanceComplexTypes/$type->id");

		$response->assertStatus(200)
			->assertJson([
				"success" => true
			]);

		$this->assertDatabaseHas("attendance_complex_types", [
			"id" => $type->id,
			"active" => true
		]);
	}
}
