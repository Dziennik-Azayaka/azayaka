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

class StudentRegistryControllerTest extends TestCase
{
	use RefreshDatabase;

	private function actingUser(): array
	{
		$user = User::factory()->create();
		$this->be($user);
		$employee = Employee::factory()->create([
			"is_admin" => true,
			"is_headmaster" => true
		]);
		$accountAccess = AccountAccess::create();
		$accountAccess->employee_id = $employee->id;
		$accountAccess->user_id = $user->id;
		$accountAccess->save();
		return ["user" => $user, "access" => $accountAccess->id];
	}

	public function test_can_list_student_registries()
	{
		$actingUser = $this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$registry = $schoolUnit->studentRegistry()->create();
		$response = $this->get("/api/studentRegistry", ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $registry->id,
			"schoolUnitId" => $schoolUnit->id,
		]);
	}

	public function test_can_create_a_student_registry()
	{
		$actingUser = $this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => $schoolUnit->id
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertCreated();
		$this->assertDatabaseHas("student_registries", [
			"school_unit_id" => $schoolUnit->id,
		]);
	}

	public function test_cannot_create_a_student_registry_without_a_valid_school_unit_id()
	{
		$actingUser = $this->actingUser();
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => "invalid-id"
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertStatus(400);
		$this->assertDatabaseMissing("student_registries", [
			"school_unit_id" => "invalid-id"
		]);
	}

	public function test_cannot_create_a_student_registry_when_the_school_unit_already_has_one() {
		$actingUser = $this->actingUser();
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		$schoolUnit->studentRegistry()->create();
		$response = $this->post("/api/studentRegistry", [
			"schoolUnitId" => $schoolUnit->id
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertStatus(409);
	}
}
