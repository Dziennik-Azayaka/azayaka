<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class StudentRegistryControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_student_registries(): void
	{
		$this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
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
		$this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
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
		$this->actingUser();
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
		$this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$schoolUnit->studentRegistry()->create();
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertStatus(409);
	}

	public function test_can_export_student_registry_as_xml(): void
	{
		$this->actingUser();

		$schoolUnit = SchoolUnit::factory()->create([
			"name" => "Szkoła im. Microsoftowców",
		]);

		$registry = $schoolUnit->studentRegistry()->create();

		$student = Student::factory()->create([
			"student_registry_id" => $registry->id,
			"first_name" => "Jan",
			"last_name" => "Kowalski",
			"second_name" => "Andrzej",
			"pesel" => "12345678901",
			"alternate_identity_document" => null,
			"birthdate" => "2010-05-15",
			"birthplace" => "Łódź",
			"admission_date" => "2025-09-01",
		]);

		Guardian::factory()->create([
			"student_id" => $student->id,
			"first_name" => "Anna",
			"last_name" => "Kowalska",
		]);

		Student::factory()->create([
			"student_registry_id" => $schoolUnit->studentRegistry()->create()->id,
			"first_name" => "Zygmunt",
			"last_name" => "Spozaszkoły",
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
		$response->assertDontSee("Excluded", false);
		/* TODO: Validate against XSD schema. Not feasible right now because PHP does not support XML 1.1 which
		the govt-provided schemas use for whatever reason. */
	}

	public function test_can_export_student_registry_as_html(): void
	{
		$this->actingUser();

		$schoolUnit = SchoolUnit::factory()->create([
			"name" => "Szkoła im. Webmasterów",
		]);

		$registry = $schoolUnit->studentRegistry()->create();

		Student::factory()->create([
			"student_registry_id" => $registry->id,
			"first_name" => "Maria",
			"last_name" => "Nowak",
			"pesel" => "98765432109",
			"alternate_identity_document" => null,
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
		$this->actingUser();

		$schoolUnit = SchoolUnit::factory()->create();
		$registry = $schoolUnit->studentRegistry()->create();

		Student::factory()->create([
			"student_registry_id" => $registry->id,
			"first_name" => "John",
			"last_name" => "Student",
			"pesel" => null,
			"alternate_identity_document" => "ABC-123",
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
		$this->actingUser();

		$schoolUnit = SchoolUnit::factory()->create();
		$registry = $schoolUnit->studentRegistry()->create();

		$response = $this->get("/api/studentRegistry/$registry->id/export?format=xml");

		$response->assertOk();
		$response->assertSee("<Uczniowie/>", false);
	}
}
