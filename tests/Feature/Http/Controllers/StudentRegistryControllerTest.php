<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Guardian;
use App\Models\Person;
use App\Models\SchoolUnit;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StudentRegistryControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_student_registries(): void
	{
		$this->actingAdminUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$registry = $schoolUnit->studentRegistry()->create();
		$response = $this->get("/api/studentRegistry");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $registry->id,
			"schoolUnitId" => $schoolUnit->id,
		]);
	}

	public function test_can_create_a_student_registry(): void
	{
		$this->actingAdminUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("student_registries", [
			"school_unit_id" => $schoolUnit->id,
		]);
	}

	public function test_cannot_create_a_student_registry_without_a_valid_school_unit_id(): void
	{
		$this->actingAdminUser();
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => "invalid-id"
		]);
		$response->assertUnprocessable();
		$this->assertDatabaseMissing("student_registries", [
			"school_unit_id" => "invalid-id"
		]);
	}

	public function test_cannot_create_a_student_registry_when_the_school_unit_already_has_one(): void
	{
		$this->actingAdminUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$schoolUnit->studentRegistry()->create();
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertStatus(409);
	}

	public function test_can_export_student_registry_as_xml(): void
	{
		$this->actingAdminUser();

		$schoolUnit = SchoolUnit::factory()->create([
			"name" => "Szkoła im. Microsoftowców",
		]);

		$registry = $schoolUnit->studentRegistry()->create();

		$person = Person::factory()->create([
			"school_unit_id" => $schoolUnit->id,
			"first_name" => "Jan",
			"last_name" => "Kowalski",
			"second_name" => "Andrzej",
			"pesel" => "12345678901",
			"alternate_identity_document" => null,
			"birthdate" => "2010-05-15",
			"birthplace" => "Łódź",
		]);

		$student = Student::factory()->create([
			"student_registry_id" => $registry->id,
			"person_id" => $person->id,
			"admission_date" => "2025-09-01",
		]);

		Guardian::factory()->create([
			"person_id" => $person->id,
			"first_name" => "Anna",
			"last_name" => "Kowalska",
		]);

		$otherSchoolUnit = SchoolUnit::factory()->create();
		$otherRegistry = $otherSchoolUnit->studentRegistry()->create();
		$otherPerson = Person::factory()->create([
			"school_unit_id" => $otherSchoolUnit->id,
			"first_name" => "Zygmunt",
			"last_name" => "Spozaszkoły",
		]);
		Student::factory()->create([
			"student_registry_id" => $otherRegistry->id,
			"person_id" => $otherPerson->id,
		]);

		$response = $this->get("/api/studentRegistry/$registry->id/export?format=xml");

		$response->assertOk();
		$response->assertHeader("Content-Type", "text/xml; charset=UTF-8");
		$this->assertStringContainsString(
			"attachment; filename=Export_Uczniow.xml",
			$response->headers->get("Content-Disposition")
		);

		$response->assertSee("<Ksiega", false);
		$response->assertSee("<Uczniow>", false);
		$response->assertSee("<Uczniowie>", false);
		$response->assertSee("<Imie>Jan</Imie>", false);
		$response->assertSee("<DrugieImie>Andrzej</DrugieImie>", false);
		$response->assertSee("<Nazwisko>Kowalski</Nazwisko>", false);
		$response->assertSee("<DataUrodzenia>2010-05-15</DataUrodzenia>", false);
		$response->assertSee("<Pesel>12345678901</Pesel>", false);
		$response->assertSee("<Imie>Anna</Imie>", false);
		/* TODO: Validate against XSD schema. Not feasible right now because PHP does not support XML 1.1 which
		the govt-provided schemas use for whatever reason. */
	}

	public function test_can_export_student_registry_as_html(): void
	{
		$this->actingAdminUser();

		$schoolUnit = SchoolUnit::factory()->create([
			"name" => "Szkoła im. Webmasterów",
		]);

		$registry = $schoolUnit->studentRegistry()->create();

		$person = Person::factory()->create([
			"school_unit_id" => $schoolUnit->id,
			"first_name" => "Maria",
			"last_name" => "Nowak",
			"pesel" => "98765432109",
			"alternate_identity_document" => null,
		]);
		Student::factory()->create([
			"student_registry_id" => $registry->id,
			"person_id" => $person->id,
		]);

		$response = $this->get("/api/studentRegistry/$registry->id/export");

		$response->assertOk();
		$response->assertHeader("Content-Type", "text/html; charset=UTF-8");
		$this->assertStringContainsString(
			"attachment; filename=Export_Uczniow.html",
			$response->headers->get("Content-Disposition")
		);
		$this->assertNotEmpty($response->getContent());
	}

	public function test_xml_export_uses_alternate_identity_document_when_student_has_no_pesel(): void
	{
		$this->actingAdminUser();

		$schoolUnit = SchoolUnit::factory()->create();
		$registry = $schoolUnit->studentRegistry()->create();

		$person = Person::factory()->create([
			"school_unit_id" => $schoolUnit->id,
			"first_name" => "John",
			"last_name" => "Student",
			"pesel" => null,
			"alternate_identity_document" => "ABC-123",
		]);
		Student::factory()->create([
			"student_registry_id" => $registry->id,
			"person_id" => $person->id,
		]);

		$response = $this->get("/api/studentRegistry/$registry->id/export?format=xml");

		$response->assertOk();
		$response->assertSee(
			"<NazwaINumerDokumentuPotwierdzajacegoTozsamosc>ABC-123</NazwaINumerDokumentuPotwierdzajacegoTozsamosc>",
			false
		);
		$response->assertDontSee("<Pesel>", false);
	}

	public function test_xml_export_returns_an_empty_students_node_when_registry_has_no_students(): void
	{
		$this->actingAdminUser();

		$schoolUnit = SchoolUnit::factory()->create();
		$registry = $schoolUnit->studentRegistry()->create();

		$response = $this->get("/api/studentRegistry/$registry->id/export?format=xml");

		$response->assertOk();
		$response->assertSee("<Uczniowie/>", false);
	}
}
