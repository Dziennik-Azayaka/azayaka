<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassUnitControllerTest extends TestCase
{
	use RefreshDatabase;

	private function actingUser(): array
	{
		$user = User::factory()->create();
		$this->be($user);
		$employee = Employee::factory()->create([
			"is_admin" => true
		]);
		$accountAccess = AccountAccess::create();
		$accountAccess->employee_id = $employee->id;
		$accountAccess->user_id = $user->id;
		$accountAccess->save();
		return ["user" => $user, "access" => $accountAccess->id];
	}

	public function test_can_list_class_units()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		ClassUnit::factory()->count(5)->create([
			"school_unit_id" => $unit->id,
		]);
		$response = $this->get("/api/schoolUnits/{$unit->id}/classUnits", ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonStructure([
			"*" => [
				"id",
				"schoolUnitId",
				"alias",
				"mark",
				"startingSchoolYear",
				"teachingCycleLength",
				"level"
			],
		]);
	}

	public function test_can_list_future_class_units()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);

		$classUnitOld = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->subYears(10)->year,
		]);
		$classUnitFuture = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->addYear()->year,
		]);

		$response = $this->get("/api/schoolUnits/{$unit->id}/classUnits?category=future", [
			"Access-ID" => $actingUser["access"]
		]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $classUnitFuture->id
		]);
		$response->assertJsonMissing([
			"id" => $classUnitOld->id
		]);
		$response->assertJsonCount(1);
	}

	public function test_can_list_current_class_units()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);

		$classUnitOld = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->subYears(10)->year,
		]);
		$classUnitFuture = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->addYear()->year,
		]);
		$classUnitCurrent = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->subYear()->year,
		]);

		$currentPeriod = new ClassificationPeriod();
		$currentPeriod->school_unit_id = $unit->id;
		$currentPeriod->school_year = Carbon::now()->year;
		$currentPeriod->period_number = 1;
		$currentPeriod->period_start = Carbon::now()->subYear()->startOfYear()->format("Y-m-d");
		$currentPeriod->period_end = Carbon::now()->addYear()->startOfYear()->format("Y-m-d");
		$currentPeriod->save();
		$periodData = [
			$currentPeriod->id => ["level" => 3]
		];
		$classUnitCurrent->periods()->sync($periodData);

		$response = $this->get("/api/schoolUnits/{$unit->id}/classUnits?category=current", [
			"Access-ID" => $actingUser["access"]
		]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $classUnitCurrent->id
		]);
		$response->assertJsonMissing([
			"id" => $classUnitOld->id
		]);
		$response->assertJsonMissing([
			"id" => $classUnitFuture->id
		]);
		$response->assertJsonCount(1);
	}

	public function test_can_list_past_class_units()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);

		$classUnitOld = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->subYears(10)->year,
		]);
		$classUnitFuture = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->addYear()->year,
		]);
		$classUnitCurrent = ClassUnit::factory()->create([
			"school_unit_id" => $unit->id,
			"starting_school_year" => Carbon::now()->subYear()->year,
		]);

		$currentPeriod = new ClassificationPeriod();
		$currentPeriod->school_unit_id = $unit->id;
		$currentPeriod->school_year = Carbon::now()->year;
		$currentPeriod->period_number = 1;
		$currentPeriod->period_start = Carbon::now()->subYears(2)->startOfYear()->format("Y-m-d");
		$currentPeriod->period_end = Carbon::now()->subYear()->startOfYear()->format("Y-m-d");
		$currentPeriod->save();
		$periodData = [
			$currentPeriod->id => ["level" => 3]
		];
		$classUnitOld->periods()->sync($periodData);

		$response = $this->get("/api/schoolUnits/{$unit->id}/classUnits?category=archive", [
			"Access-ID" => $actingUser["access"]
		]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonFragment([
			"id" => $classUnitOld->id
		]);
		$response->assertJsonMissing([
			"id" => $classUnitFuture->id
		]);
		$response->assertJsonMissing([
			"id" => $classUnitCurrent->id
		]);
		$response->assertJsonCount(1);
	}

	public function test_can_create_a_class_unit()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$employee = Employee::factory()->create();
		$currentYear = Carbon::now()->year;
		$classificationPeriod1 = new ClassificationPeriod();
		$classificationPeriod1->school_unit_id = $unit->id;
		$classificationPeriod1->school_year = $currentYear;
		$classificationPeriod1->period_number = 1;
		$classificationPeriod1->period_start = Carbon::create($currentYear, 9);
		$classificationPeriod1->period_end = Carbon::create($currentYear + 1, 2);
		$classificationPeriod1->save();

		$classificationPeriod2 = new ClassificationPeriod();
		$classificationPeriod2->school_unit_id = $unit->id;
		$classificationPeriod2->school_year = $currentYear;
		$classificationPeriod2->period_number = 2;
		$classificationPeriod2->period_start = Carbon::create($currentYear + 1, 2, 2);
		$classificationPeriod2->period_end = Carbon::create($currentYear + 1, 8, 31);
		$classificationPeriod2->save();

		$response = $this->post("/api/schoolUnits/{$unit->id}/classUnits", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a",
			"startingSchoolYear" => $currentYear,
			"teachingCycleLength" => 5,
			"employeeIds" => [
				$employee->id
			],
			"promoteEvery" => "year"
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$this->assertDatabaseHas("class_units", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a",
			"starting_school_year" => $currentYear,
			"teaching_cycle_length" => 5,
			"promote_every" => "year"
		]);
		$this->assertDatabaseHas("class_units_employees", [
			"employee_id" => $employee->id
		]);

		$this->assertDatabaseHas("class_units_periods", [
			"classification_period_id" => $classificationPeriod1->id,
			"level" => 1
		]);
		$this->assertDatabaseHas("class_units_periods", [
			"classification_period_id" => $classificationPeriod2->id,
			"level" => 1
		]);
	}

	public function test_can_create_a_class_unit_with_promotion_every_semester()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$employee = Employee::factory()->create();
		$currentYear = Carbon::now()->year;
		$classificationPeriod1 = new ClassificationPeriod();
		$classificationPeriod1->school_unit_id = $unit->id;
		$classificationPeriod1->school_year = $currentYear;
		$classificationPeriod1->period_number = 1;
		$classificationPeriod1->period_start = Carbon::create($currentYear, 9);
		$classificationPeriod1->period_end = Carbon::create($currentYear + 1, 2);
		$classificationPeriod1->save();

		$classificationPeriod2 = new ClassificationPeriod();
		$classificationPeriod2->school_unit_id = $unit->id;
		$classificationPeriod2->school_year = $currentYear;
		$classificationPeriod2->period_number = 2;
		$classificationPeriod2->period_start = Carbon::create($currentYear + 1, 2, 2);
		$classificationPeriod2->period_end = Carbon::create($currentYear + 1, 8, 31);
		$classificationPeriod2->save();

		$response = $this->post("/api/schoolUnits/{$unit->id}/classUnits", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a",
			"startingSchoolYear" => $currentYear,
			"teachingCycleLength" => 5,
			"employeeIds" => [
				$employee->id
			],
			"promoteEvery" => "semester"
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$this->assertDatabaseHas("class_units", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a",
			"starting_school_year" => $currentYear,
			"teaching_cycle_length" => 5,
			"promote_every" => "semester"
		]);
		$this->assertDatabaseHas("class_units_employees", [
			"employee_id" => $employee->id
		]);

		$this->assertDatabaseHas("class_units_periods", [
			"classification_period_id" => $classificationPeriod1->id,
			"level" => 1
		]);
		$this->assertDatabaseHas("class_units_periods", [
			"classification_period_id" => $classificationPeriod2->id,
			"level" => 2
		]);
	}

	public function test_creating_a_class_unit_with_disabled_employees_fails()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$disabledEmployee = Employee::factory()->create(["active" => false]);
		$activeEmployee = Employee::factory()->create();
		$response = $this->post("/api/schoolUnits/{$unit->id}/classUnits", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a",
			"startingSchoolYear" => Carbon::now()->year,
			"teachingCycleLength" => 5,
			"employeeIds" => [
				$activeEmployee->id,
				$disabledEmployee->id
			]
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertBadRequest();
		$this->assertDatabaseMissing("class_units", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a"
		]);
		$this->assertDatabaseMissing("class_units_employees", [
			"employee_id" => $activeEmployee->id,
		]);
		$this->assertDatabaseMissing("class_units_employees", [
			"employee_id" => $disabledEmployee->id,
		]);
	}

	public function test_creating_a_class_unit_with_nonexistent_employees_fails()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$employee = Employee::factory()->create();
		$response = $this->post("/api/schoolUnits/{$unit->id}/classUnits", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a",
			"startingSchoolYear" => Carbon::now()->year,
			"teachingCycleLength" => 5,
			"employeeIds" => [
				$employee->id,
				694202137
			]
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertBadRequest();
		$this->assertDatabaseMissing("class_units", [
			"alias" => "Klasa Informatyczna",
			"mark" => "a"
		]);
		$this->assertDatabaseMissing("class_units_employees", [
			"employee_id" => $employee->id,
		]);
		$this->assertDatabaseMissing("class_units_employees", [
			"employee_id" => 694202137,
		]);
	}

	public function test_can_update_a_class_unit()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$oldEmployee = Employee::factory()->create();
		$newEmployee = Employee::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $unit->id]);
		$classUnit->employees()->attach($oldEmployee->id);

		$response = $this->put("/api/schoolUnits/{$classUnit->id}/classUnits/{$classUnit->id}", [
			"alias" => "Klasa Informatyczna",
			"mark" => "y",
			"employeeIds" => [
				$newEmployee->id
			],
			"startingSchoolYear" => Carbon::now()->year,
			"teachingCycleLength" => 5
		], ["Access-ID" => $actingUser["access"]]);

		$response->assertOk();
		$this->assertDatabaseHas("class_units", [
			"alias" => "Klasa Informatyczna",
			"mark" => "y",
		]);
		$this->assertDatabaseHas("class_units_employees", [
			"employee_id" => $newEmployee->id,
			"class_unit_id" => $classUnit->id,
		]);
		$this->assertDatabaseMissing("class_units_employees", [
			"employee_id" => $oldEmployee->id,
			"class_unit_id" => $classUnit->id,
		]);
	}

	public function test_can_delete_a_class_unit()
	{
		$actingUser = $this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $unit->id]);
		$employee = Employee::factory()->create();
		$classUnit->employees()->attach($employee->id);
		$response = $this->delete("/api/schoolUnits/{$unit->id}/classUnits/{$classUnit->id}", [], [
			"Access-ID" => $actingUser["access"]
		]);
		$response->assertOk();
		$this->assertDatabaseMissing("class_units", [
			"alias" => $classUnit->alias,
			"mark" => $classUnit->mark,
			"id" => $classUnit->id,
		]);
		$this->assertDatabaseMissing("class_units_employees", [
			"employee_id" => $employee->id,
			"class_unit_id" => $classUnit->id,
		]);
	}
}
