<?php

namespace Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\ClassificationPeriod;
use App\Models\ClassificationPeriodDefaults;
use App\Models\ClassUnit;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassificationPeriodControllerTest extends TestCase
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

	public function test_can_list_classification_periods()
	{
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$periodOne = new ClassificationPeriod();
		$periodOne->school_year = 2025;
		$periodOne->period_number = 1;
		$periodOne->class_unit_id = $classUnit->id;
		$periodOne->period_start = "2025-09-01";
		$periodOne->period_end = "2025-12-31";
		$periodOne->save();

		$periodTwo = new ClassificationPeriod();
		$periodTwo->school_year = 2025;
		$periodTwo->period_number = 2;
		$periodTwo->class_unit_id = $classUnit->id;
		$periodTwo->period_start = "2026-01-01";
		$periodTwo->period_end = "2026-08-31";
		$periodTwo->save();

		$response = $this->get("/api/classUnits/$classUnit->id/classificationPeriods/2025");
		$response->assertOk();
		$response->assertJsonStructure([
			"*" => [
				"id",
				"classUnitId",
				"schoolYear",
				"periodNumber",
				"periodStart",
				"periodEnd",
			]
		]);
	}

	public function test_can_save_new_classification_periods()
	{
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$response = $this->post("/api/classUnits/$classUnit->id/classificationPeriods/2025", [
			"periodEnd" => [
				"2025-12-31",
				"2026-03-01"
			]
		]);
		$response->assertOk();
		$this->assertDatabaseHas("classification_periods", [
			"school_year" => 2025,
			"period_number" => 1,
			"class_unit_id" => $classUnit->id,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31",
		]);

		$this->assertDatabaseHas("classification_periods", [
			"school_year" => 2025,
			"period_number" => 2,
			"class_unit_id" => $classUnit->id,
			"period_start" => "2026-01-01",
			"period_end" => "2026-03-01",
		]);

		$this->assertDatabaseHas("classification_periods", [
			"school_year" => 2025,
			"period_number" => 3,
			"class_unit_id" => $classUnit->id,
			"period_start" => "2026-03-02",
			"period_end" => "2026-08-31",
		]);
	}

	public function test_can_delete_classification_periods() {
		$this->actingUser();
		$complex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$periodOne = new ClassificationPeriod();
		$periodOne->school_year = 2025;
		$periodOne->period_number = 1;
		$periodOne->class_unit_id = $classUnit->id;
		$periodOne->period_start = "2025-09-01";
		$periodOne->period_end = "2025-12-31";
		$periodOne->save();

		$periodTwo = new ClassificationPeriod();
		$periodTwo->school_year = 2025;
		$periodTwo->period_number = 2;
		$periodTwo->class_unit_id = $classUnit->id;
		$periodTwo->period_start = "2026-01-01";
		$periodTwo->period_end = "2026-08-31";
		$periodTwo->save();

		$response = $this->delete("/api/classUnits/$classUnit->id/classificationPeriods/2025");
		$response->assertOk();
		$this->assertDatabaseMissing("classification_periods", [
			"school_year" => $periodOne->school_year,
			"period_number" => $periodOne->period_number,
			"class_unit_id" => $classUnit->id,
			"period_start" => $periodOne->period_start,
			"period_end" => $periodOne->period_end,
			"id" => $periodOne->id,
		]);

		$this->assertDatabaseMissing("classification_periods", [
			"school_year" => $periodTwo->school_year,
			"period_number" => $periodTwo->period_number,
			"class_unit_id" => $classUnit->id,
			"period_start" => $periodTwo->period_start,
			"period_end" => $periodTwo->period_end,
			"id" => $periodTwo->id,
		]);
	}
}
