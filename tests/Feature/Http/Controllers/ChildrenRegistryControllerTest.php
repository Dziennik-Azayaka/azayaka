<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Child;
use App\Models\CompulsoryEducationFulfillment;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\SchoolUnit;
use Database\Factories\CompulsoryEducationFulfillmentFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ChildrenRegistryControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_children_registries(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$registry = $schoolUnit->childrenRegistry()->create();
		$response = $this->get("/api/childrenRegistry");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $registry->id,
			"schoolUnitId" => $schoolUnit->id,
		]);
	}

	public function test_can_create_a_children_registry(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$response = $this->post("/api/childrenRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("children_registries", [
			"school_unit_id" => $schoolUnit->id,
		]);
	}

	public function test_cannot_create_a_children_registry_without_a_valid_school_unit_id(): void
	{
		$this->actingUser();
		$response = $this->post("/api/childrenRegistry", [
			"schoolUnitId" => "invalid-id"
		]);
		$response->assertUnprocessable();
		$this->assertDatabaseMissing("children_registries", [
			"school_unit_id" => "invalid-id"
		]);
	}

	public function test_cannot_create_a_children_registry_when_the_school_unit_already_has_one(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$schoolUnit->childrenRegistry()->create();
		$response = $this->post("/api/childrenRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertStatus(409);
	}

	public function test_can_export_children_registry_as_xml(): void
	{
		$this->actingUser();

		$schoolUnit = SchoolUnit::factory()->create([
			"name" => "Szkoła im. Microsoftowców",
		]);

		$registry = $schoolUnit->childrenRegistry()->create();

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

		$child = Child::factory()->create([
			"children_registry_id" => $registry->id,
			"person_id" => $person->id
		]);

		CompulsoryEducationFulfillment::factory()->create([
			"child_id" => $child->id,
			"postponement_info" => "Odroczony o 2 lata"
		]);

		Guardian::factory()->create([
			"person_id" => $person->id,
			"first_name" => "Anna",
			"last_name" => "Kowalska",
		]);

		$response = $this->get("/api/childrenRegistry/$registry->id/export?format=xml");

		$response->assertOk();
		$response->assertHeader("Content-Type", "text/xml; charset=UTF-8");
		$this->assertStringContainsString(
			"attachment; filename=Export_EwidencjiDzieci.xml",
			$response->headers->get("Content-Disposition")
		);

		$response->assertSee("<Ksiega", false);
		$response->assertSee("<Dzieci>", false);
		$response->assertSee("<Dziecko id=\"$child->id\">", false);
		$response->assertSee("<Imie>Jan</Imie>", false);
		$response->assertSee("<DrugieImie>Andrzej</DrugieImie>", false);
		$response->assertSee("<Nazwisko>Kowalski</Nazwisko>", false);
		$response->assertSee("<DataUrodzenia>2010-05-15</DataUrodzenia>", false);
		$response->assertSee("<Pesel>12345678901</Pesel>", false);
		$response->assertSee("<Imie>Anna</Imie>", false);
		$response->assertSee("<InformacjeOOdroczeniu>Odroczony o 2 lata</InformacjeOOdroczeniu>", false);
		/* TODO: Validate against XSD schema. Not feasible right now because PHP does not support XML 1.1 which
		the govt-provided schemas use for whatever reason. */
	}
}
