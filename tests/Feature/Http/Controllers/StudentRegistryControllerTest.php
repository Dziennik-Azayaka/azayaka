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
}
