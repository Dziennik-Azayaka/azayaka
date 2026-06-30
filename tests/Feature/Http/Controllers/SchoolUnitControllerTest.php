<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SchoolUnitControllerTest extends TestCase
{
	use RefreshDatabase;

	private function validPayload(array $overrides = []): array
	{
		return array_merge([
			"name" => "Technikum nr 1",
			"type" => SchoolType::TECHNIKUM->value,
			"studentCategory" => "childrenAndYouths",
			"municipality" => "Łódź",
			"voivodeship" => Voivodeship::LODZKIE->value,
			"town" => "Łódź",
			"district" => "Bałuty",
			"postalCode" => "90-001",
			"post" => "Łódź",
			"street" => "ul. Dzienniczkowa",
			"houseNumber" => "23",
			"flatNumber" => null,
			"shortName" => "TECH 1",
			"schoolComplexId" => null,
		], $overrides);
	}

	public function test_list_returns_units_resource_collection(): void
	{
		$this->actingUser();
		SchoolUnit::factory()->count(2)->create(["school_complex_id" => null]);

		$response = $this->get("/api/schoolUnits");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonStructure([
			"*" => [
				"id",
				"name",
				"active",
				"type",
				"studentCategory",
				"municipality",
				"voivodeship",
				"town",
				"district",
				"postalCode",
				"post",
				"street",
				"houseNumber",
				"flatNumber",
				"shortName",
				"schoolComplexId",
			],
		]);
	}

	public function test_create_persists_valid_school_unit(): void
	{
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();

		$payload = $this->validPayload(["schoolComplexId" => $complex->id]);
		$response = $this->post("/api/schoolUnits", $payload);

		$response->assertCreated();
		$response->assertJson(["success" => true]);
		$this->assertDatabaseHas("school_units", [
			"name" => $payload["name"],
			"type" => $payload["type"],
			"student_category" => $payload["studentCategory"],
			"municipality" => $payload["municipality"],
			"voivodeship" => $payload["voivodeship"],
			"town" => $payload["town"],
			"district" => $payload["district"],
			"postal_code" => $payload["postalCode"],
			"post" => $payload["post"],
			"street" => $payload["street"],
			"house_number" => $payload["houseNumber"],
			"short_name" => $payload["shortName"],
			"school_complex_id" => $complex->id,
		]);
	}

	public function test_create_rejects_invalid_student_category(): void
	{
		$this->actingUser();
		$payload = $this->validPayload(["studentCategory" => "invalid-type"]);

		$response = $this->post("/api/schoolUnits", $payload);
		$response->assertStatus(422);
		$response->assertJson([
			"success" => false,
			"errors" => ["STUDENT_CATEGORY_THE_SELECTED_STUDENT_CATEGORY_IS_INVALID"],
		]);
	}

	public function test_create_blocks_multiple_units_without_parent(): void
	{
		$this->actingUser();
		SchoolUnit::factory()->count(2)->create(["school_complex_id" => null]);

		$payload = $this->validPayload(["schoolComplexId" => null]);
		$response = $this->post("/api/schoolUnits", $payload);

		$response->assertOk();
		$response->assertJson([
			"success" => false,
			"errors" => ["CANNOT_CREATE_MULTIPLE_SCHOOL_UNITS_WITHOUT_PARENT"],
		]);
	}

	public function test_archive_toggles_active_and_blocks_update_when_inactive(): void
	{
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);

		$response = $this->put("/api/schoolUnits/$unit->id/activity", [
			"password" => "password", // current_password rule
		]);
		$response->assertOk();
		$response->assertJson(["success" => true]);
		$this->assertDatabaseHas("school_units", [
			"id" => $unit->id,
			"active" => false,
		]);

		$updatePayload = $this->validPayload(["name" => "Updated Name", "schoolComplexId" => $complex->id]);
		$updateResponse = $this->put("/api/schoolUnits/$unit->id", $updatePayload);
		$updateResponse->assertOk();
		$updateResponse->assertJson([
			"success" => false,
			"errors" => ["SCHOOL_UNIT_NOT_ACTIVE"],
		]);

		$response = $this->put("/api/schoolUnits/$unit->id/activity", [
			"password" => "password",
		]);
		$response->assertOk();
		$this->assertDatabaseHas("school_units", [
			"id" => $unit->id,
			"active" => true,
		]);

		$updateResponse = $this->put("/api/schoolUnits/$unit->id", $updatePayload);
		$updateResponse->assertOk();
		$updateResponse->assertJson(["success" => true]);
		$this->assertDatabaseHas("school_units", [
			"id" => $unit->id,
			"name" => "Updated Name",
		]);
	}
}
