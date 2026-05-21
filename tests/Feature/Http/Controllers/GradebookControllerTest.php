<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use App\Models\SchoolUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class GradebookControllerTest extends TestCase
{
	public function test_can_list_gradebooks(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$gradebook = Gradebook::factory()->create(["class_unit_id" => $classUnit->id]);
		$response = $this->get("/api/schoolUnits/$schoolUnit->id/gradebooks");
		$response->assertOk();
		$response->assertJsonCount(1);
		$response->assertJsonFragment([
			"id" => $gradebook->id,
			"schoolYear" => $gradebook->startingClassificationPeriod->school_year,
			"level" => $gradebook->level
		]);
	}

	public function test_can_list_gradebooks_by_class_unit_and_school_year(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit1 = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classUnit2 = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classificationPeriod1 = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		$classificationPeriod2 = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2024,
			"period_number" => 1,
			"period_start" => "2024-09-01",
			"period_end" => "2024-12-31"
		]);

		$gradebookCorrect = Gradebook::factory()->create([
			"class_unit_id" => $classUnit1->id,
			"classification_period_id" => $classificationPeriod1->id
		]);
		$gradebookIncorrectClass = Gradebook::factory()->create([
			"class_unit_id" => $classUnit2->id,
			"classification_period_id" => $classificationPeriod1->id
		]);
		$gradebookIncorrectPeriod = Gradebook::factory()->create([
			"class_unit_id" => $classUnit1->id,
			"classification_period_id" => $classificationPeriod2->id
		]);

		$response = $this->get("/api/schoolUnits/$schoolUnit->id/gradebooks?classUnitId=$classUnit1->id&schoolYear=2025");
		$response->assertOk();
		$response->assertJsonCount(1);
		$response->assertJsonFragment(["id" => $gradebookCorrect->id]);
		$response->assertJsonMissing(["id" => $gradebookIncorrectClass->id]);
		$response->assertJsonMissing(["id" => $gradebookIncorrectPeriod->id]);
	}

	public function test_can_create_gradebook(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		$response = $this->post("/api/gradebooks", [
			"classUnitId" => $classUnit->id,
			"classificationPeriodId" => $classificationPeriod->id,
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("gradebooks", [
			"class_unit_id" => $classUnit->id,
			"classification_period_id" => $classificationPeriod->id,
		]);
	}

	public function test_creating_gradebook_when_one_already_exists_for_classification_period_fails(): void
	{
		$this->actingUser();
		$schoolUnit = SchoolUnit::factory()->create();
		$classUnit = ClassUnit::factory()->create(["school_unit_id" => $schoolUnit->id]);
		$classificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => $schoolUnit->id,
			"school_year" => 2025,
			"period_number" => 1,
			"period_start" => "2025-09-01",
			"period_end" => "2025-12-31"
		]);
		Gradebook::factory()->create([
			"class_unit_id" => $classUnit->id,
			"classification_period_id" => $classificationPeriod->id
		]);
		$response = $this->post("/api/gradebooks", [
			"classUnitId" => $classUnit->id,
			"classificationPeriodId" => $classificationPeriod->id,
		]);
		$response->assertConflict();
	}
}
