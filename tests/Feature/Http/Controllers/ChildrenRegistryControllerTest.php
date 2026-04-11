<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChildrenRegistryControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_list_children_registries()
	{
		$this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$registry = $schoolUnit->childrenRegistry()->create();
		$response = $this->get("/api/childrenRegistry");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $registry->id,
			"schoolUnitId" => $schoolUnit->id,
		]);
	}

	public function test_can_create_a_children_registry()
	{
		$this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$response = $this->post("/api/childrenRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("children_registries", [
			"school_unit_id" => $schoolUnit->id,
		]);
	}

	public function test_cannot_create_a_children_registry_without_a_valid_school_unit_id()
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

	public function test_cannot_create_a_children_registry_when_the_school_unit_already_has_one()
	{
		$this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$schoolUnit->childrenRegistry()->create();
		$response = $this->post("/api/childrenRegistry", [
			"schoolUnitId" => $schoolUnit->id
		]);
		$response->assertStatus(409);
	}
}
