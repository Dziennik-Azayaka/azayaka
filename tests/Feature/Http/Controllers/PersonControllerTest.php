<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Models\Person;
use App\Models\SchoolUnit;
use App\Models\StudentRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

final class PersonControllerTest extends TestCase
{
	use RefreshDatabase;

	private function createSchoolUnit(): SchoolUnit
	{
		return SchoolUnit::factory()->create();
	}

	private function personPayload(array $overrides = []): array
	{
		return array_merge([
			"firstName" => "Jan",
			"lastName" => "Kowalski",
			"pesel" => "55082669838",
			"birthdate" => "2010-01-02",
			"birthplace" => "Łódź",
			"gender" => "male",
			"residenceAddressCountry" => "PL",
		], $overrides);
	}

	public function test_can_create_person(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people", $this->personPayload());
		$response->assertCreated();
		$this->assertDatabaseHas("people", [
			"first_name" => "Jan",
			"last_name" => "Kowalski",
			"pesel" => "55082669838",
			"school_unit_id" => $schoolUnit->id,
		]);
		$this->assertDatabaseHas("residence_addresses", [
			"country" => "PL",
		]);
	}

	public function test_can_create_person_with_student_registry(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$registry = $schoolUnit->studentRegistry()->create();
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people", $this->personPayload([
			"studentRegistryId" => $registry->id,
			"admissionDate" => "2025-09-01",
		]));
		$response->assertCreated();
		$this->assertDatabaseHas("students", [
			"student_registry_id" => $registry->id,
			"admission_date" => "2025-09-01",
		]);
	}

	public function test_can_create_person_with_children_registry(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$registry = $schoolUnit->childrenRegistry()->create();
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people", $this->personPayload([
			"childrenRegistryId" => $registry->id,
		]));
		$response->assertCreated();
		$this->assertDatabaseHas("children", [
			"children_registry_id" => $registry->id,
		]);
	}

	public function test_cannot_create_person_without_required_fields(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people", [
			"firstName" => "Jan",
		]);
		$response->assertUnprocessable();
	}

	public function test_can_create_person_with_alternate_identity_document(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people", $this->personPayload([
			"pesel" => null,
			"alternateIdentityDocument" => "ABC-123",
		]));
		$response->assertCreated();
		$this->assertDatabaseHas("people", [
			"alternate_identity_document" => "ABC-123",
			"pesel" => null,
			"school_unit_id" => $schoolUnit->id,
		]);
	}

	public function test_can_update_person(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$person = Person::factory()->create([
			"school_unit_id" => $schoolUnit->id,
		]);
		$response = $this->put("/api/schoolUnits/$schoolUnit->id/people/$person->id", $this->personPayload([
			"firstName" => "Jan",
			"lastName" => "Nowak",
		]));
		$response->assertOk();
		$this->assertDatabaseHas("people", [
			"id" => $person->id,
			"first_name" => "Jan",
			"last_name" => "Nowak",
		]);
	}

	public function test_can_delete_person(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$person = Person::factory()->create([
			"school_unit_id" => $schoolUnit->id,
		]);
		$response = $this->delete("/api/people/$person->id");
		$response->assertOk();
		$this->assertDatabaseMissing("people", [
			"id" => $person->id,
		]);
	}

	public function test_can_lookup_person_by_pesel(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$person = Person::factory()->create([
			"school_unit_id" => $schoolUnit->id,
			"pesel" => "55082669838",
		]);
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people/lookup", [
			"pesel" => "55082669838",
		]);
		$response->assertOk();
		$response->assertJsonFragment([
			"found" => true,
			"id" => $person->id,
		]);
	}

	public function test_lookup_returns_not_found_for_unknown_pesel(): void
	{
		$this->actingAdminUser();
		$schoolUnit = $this->createSchoolUnit();
		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people/lookup", [
			"pesel" => "55082669838",
		]);
		$response->assertOk();
		$response->assertJsonFragment([
			"found" => false,
		]);
	}

	private function generateCsv(array $headers, array $rows): string
	{
		$content = implode(",", $headers) . "\n";
		foreach ($rows as $row) {
			$content .= implode(",", $row) . "\n";
		}
		return $content;
	}

	public function test_can_import_people_successfully()
	{
		$this->actingAdminUser();
		$schoolUnitId = SchoolUnit::factory()->create()->id;
		$csvContent = $this->generateCsv(
			["firstName", "lastName", "pesel", "birthdate", "birthplace", "residenceAddressCountry"],
			[
				["Jan", "Kowalski", "08290823273", "2010-01-01", "Łódź", "Polska"],
				["Tadeusz", "Nowak", "55082669838", "2011-05-05", "Sosnowiec", "Polska"]
			]
		);

		$file = UploadedFile::fake()->createWithContent("people.csv", $csvContent);

		$response = $this->post("/api/schoolUnits/$schoolUnitId/people/import", [
			"csvFile" => $file
		]);

		$response->assertStatus(201);
		$response->assertJson([
			"success" => true,
			"importedCount" => 2
		]);

		$this->assertDatabaseHas("people", [
			"school_unit_id" => $schoolUnitId,
			"first_name" => "Jan",
			"pesel" => "08290823273",
		]);

		$this->assertDatabaseHas("people", [
			"school_unit_id" => $schoolUnitId,
			"first_name" => "Tadeusz",
			"pesel" => "55082669838",
		]);

		$this->assertDatabaseCount("residence_addresses", 2);
	}

	public function test_import_can_import_people_and_assign_to_registries()
	{
		$this->actingAdminUser();
		$studentRegistry = StudentRegistry::factory()->create();
		$childrenRegistry = ChildrenRegistry::factory()->create([
			"school_unit_id" => $studentRegistry->school_unit_id,
		]);

		$csvContent = $this->generateCsv(
			["firstName", "lastName", "alternateIdentityDocument", "birthdate", "birthplace", "residenceAddressCountry", "admissionDate"],
			[
				["Grzegorz", "Nowak", "ABC123", "2012-03-03", "Łódź", "Polska", "2023-09-01"]
			]
		);

		$file = UploadedFile::fake()->createWithContent("people.csv", $csvContent);

		$response = $this->post("/api/schoolUnits/$studentRegistry->school_unit_id/people/import", [
			"csvFile" => $file,
			"studentRegistryId" => $studentRegistry->id,
			"childrenRegistryId" => $childrenRegistry->id,
		]);

		$response->assertStatus(201);

		$person = Person::where("alternate_identity_document", "ABC123")->first();

		$this->assertDatabaseHas("students", [
			"person_id" => $person->id,
			"student_registry_id" => $studentRegistry->id,
			"admission_date" => "2023-09-01"
		]);

		$this->assertDatabaseHas("children", [
			"person_id" => $person->id,
			"children_registry_id" => $childrenRegistry->id,
		]);
	}

	public function test_import_fails_when_missing_required_fields_in_csv()
	{
		$this->actingAdminUser();
		$schoolUnitId = SchoolUnit::factory()->create()->id;
		$csvContent = $this->generateCsv(
			["firstName", "lastName", "pesel", "birthdate", "birthplace", "residenceAddressCountry"],
			[
				["", "Doe", "08290823273", "2010-01-01", "Warsaw", "Polska"]
			]
		);

		$file = UploadedFile::fake()->createWithContent("people.csv", $csvContent);

		$response = $this->post("/api/schoolUnits/$schoolUnitId/people/import", [
			"csvFile" => $file,
		]);

		$response->assertStatus(422);
		$response->assertJson([
			"success" => false,
		]);

		$this->assertStringContainsString("Rząd 2", $response->json("errors.0"));
		$this->assertDatabaseCount("people", 0);
	}

	public function test_import_fails_when_csv_contains_duplicates_inside_itself()
	{
		$this->actingAdminUser();
		$schoolUnitId = SchoolUnit::factory()->create()->id;
		$csvContent = $this->generateCsv(
			["firstName", "lastName", "pesel", "birthdate", "birthplace", "residenceAddressCountry"],
			[
				["Jan", "Kowalski", "08290823273", "2010-01-01", "Łódź", "Polska"],
				["Tadeusz", "Nowak", "08290823273", "2011-05-05", "Sosnowiec", "Polska"]
			]
		);

		$file = UploadedFile::fake()->createWithContent("people.csv", $csvContent);

		$response = $this->post("/api/schoolUnits/$schoolUnitId/people/import", [
			"csvFile" => $file,
		]);

		$response->assertStatus(422);

		$this->assertStringContainsString("Rząd 3:", $response->json("errors.0"));
		$this->assertDatabaseCount("people", 0);
	}

	public function test_import_fails_when_csv_contains_duplicates_against_database()
	{
		$this->actingAdminUser();
		$schoolUnitId = SchoolUnit::factory()->create()->id;
		Person::factory()->create([
			"school_unit_id" => $schoolUnitId,
			"pesel" => "08290823273"
		]);

		$csvContent = $this->generateCsv(
			["firstName", "lastName", "pesel", "birthdate", "birthplace", "residenceAddressCountry"],
			[
				["Jan", "Kowalski", "08290823273", "2010-01-01", "Łódź", "Polska"]
			]
		);

		$file = UploadedFile::fake()->createWithContent("people.csv", $csvContent);

		$response = $this->post("/api/schoolUnits/$schoolUnitId/people/import", [
			"csvFile" => $file,
		]);

		$response->assertStatus(422);
		$this->assertStringContainsString("Rząd 2", $response->json("errors.0"));
	}

	public function test_import_fails_when_importing_to_archived_registry()
	{
		$this->actingAdminUser();
		$schoolUnit = SchoolUnit::factory()->create(["active" => false]);
		$studentRegistry = StudentRegistry::factory()->create(["school_unit_id" => $schoolUnit->id]);

		$csvContent = $this->generateCsv(
			["firstName", "lastName", "alternateIdentityDocument", "birthdate", "birthplace", "residenceAddressCountry", "admissionDate"],
			[
				["Grzegorz", "Nowak", "ABC123", "2012-03-03", "Łódź", "Polska", "2023-09-01"]
			]
		);

		$file = UploadedFile::fake()->createWithContent("people.csv", $csvContent);

		$response = $this->post("/api/schoolUnits/$schoolUnit->id/people/import", [
			"csvFile" => $file,
			"studentRegistryId" => $studentRegistry->id
		]);

		$response->assertStatus(422);

		$response->assertJson([
			"success" => false,
			"errors" => [
				"REGISTRY_ARCHIVED"
			]
		]);

		$this->assertDatabaseCount("people", 0);
	}
}
