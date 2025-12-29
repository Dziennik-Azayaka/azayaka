<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\ClassificationPeriodDefaults;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassificationPeriodDefaultsControllerTest extends TestCase
{
	use RefreshDatabase;

	private function actingUser(): User
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
		return $user;
	}

	public function test_can_list_classification_period_defaults()
	{
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$periodOne = new ClassificationPeriodDefaults();
		$periodOne->school_year = 2025;
		$periodOne->period_number = 1;
		$periodOne->school_unit_id = $unit->id;
		$periodOne->period_start = "2025-09-01";
		$periodOne->period_end = "2025-12-31";
		$periodOne->save();

		$periodTwo = new ClassificationPeriodDefaults();
		$periodTwo->school_year = 2025;
		$periodTwo->period_number = 2;
		$periodTwo->school_unit_id = $unit->id;
		$periodTwo->period_start = "2026-01-01";
		$periodTwo->period_end = "2026-08-31";
		$periodTwo->save();

		$response = $this->get("/api/classificationPeriods/defaults");
		$response->assertOk();
		$response->assertJsonStructure([
			"*" => [
				"*" => [
					"id",
					"schoolUnitId",
					"schoolYear",
					"periodNumber",
					"periodStart",
					"periodEnd",
				],
			]
		]);
	}

	public function test_can_save_new_classification_period_defaults() {
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$response = $this->post("/api/classificationPeriods/defaults/2025/{$unit->id}", [
			"periodEnd" => [
				"2025-12-31",
				"2026-03-01"
			]
		]);
		$response->assertOk();
		$this->assertDatabaseHas("classification_period_defaults", [
			"school_year" => 2025,
			"period_number" => 1,
			"school_unit_id" => $unit->id,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31",
		]);

		$this->assertDatabaseHas("classification_period_defaults", [
			"school_year" => 2025,
			"period_number" => 2,
			"school_unit_id" => $unit->id,
			"period_start" => "2026-01-01",
			"period_end" => "2026-03-01",
		]);

		$this->assertDatabaseHas("classification_period_defaults", [
			"school_year" => 2025,
			"period_number" => 3,
			"school_unit_id" => $unit->id,
			"period_start" => "2026-03-02",
			"period_end" => "2026-08-31",
		]);
	}

	public function test_saving_invalid_period_defaults_fails() {
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$response = $this->post("/api/classificationPeriods/defaults/2025/{$unit->id}", [
			"periodEnd" => [
				"2025-12-31",
				"2025-07-01"
			]
		]);
		$response->assertBadRequest();
		$this->assertDatabaseMissing("classification_period_defaults", [
			"school_year" => 2025,
			"period_number" => 1,
			"school_unit_id" => $unit->id,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31",
		]);
	}
}
