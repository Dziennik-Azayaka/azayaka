<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Models\CompulsoryEducationFulfillment;
use App\Models\SchoolUnit;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompulsoryEducationFulfillmentControllerTest extends TestCase
{
	use RefreshDatabase;

    public function test_can_create_compulsory_education_fulfillment()
    {
		$this->actingUser();
		$childrenRegistry = ChildrenRegistry::create([
			"school_unit_id" => SchoolUnit::factory()->create()->id,
		]);
		$student = Student::factory()->create([
			"children_registry_id" => $childrenRegistry->id
		]);
		$payload = [
			"schoolYear" => 2025,
			"controlDate" => Carbon::now()->format("Y-m-d"),
			"fulfillmentForm" => "w szkole w której obwodzie mieszka uczeń",
			"level" => 5,
			"relationship" => "podlega obowiązku szkolnemu w szkole podstawowej"
		];
		$response = $this->post("/api/childrenRegistry/$childrenRegistry->id/$student->id/fulfillment", $payload);
		$response->assertCreated();
		$payload["children_registry_id"] = $childrenRegistry->id;
		$payload["student_id"] = $student->id;
		$this->assertDatabaseHas("compulsory_education_fulfillments", [
			"school_year" => $payload["schoolYear"],
			"control_date" => $payload["controlDate"],
			"fulfillment_form" => $payload["fulfillmentForm"],
			"level" => $payload["level"],
			"relationship" => $payload["relationship"]
		]);
    }

	public function test_can_update_compulsory_education_fulfillment()
	{
		$this->actingUser();
		$childrenRegistry = ChildrenRegistry::create([
			"school_unit_id" => SchoolUnit::factory()->create()->id,
		]);
		$student = Student::factory()->create([
			"children_registry_id" => $childrenRegistry->id,
		]);
		$fulfillment = CompulsoryEducationFulfillment::factory()->recycle($student)->create([
			"children_registry_id" => $childrenRegistry->id,
		]);
		$updatedPayload = [
			"schoolYear" => 2026,
			"controlDate" => Carbon::now()->addYear()->format("Y-m-d"),
			"fulfillmentForm" => "w szkole poza obwodem w którym mieszka uczeń",
			"level" => 2,
			"relationship" => "podlega obowiązkowi szkolnemu w szkole ponadpodstawowej"
		];
		$response = $this->put("/api/childrenRegistry/$childrenRegistry->id/$student->id/fulfillment/$fulfillment->id", $updatedPayload);
		$response->assertOk();
		$this->assertDatabaseHas("compulsory_education_fulfillments", [
			"id" => $fulfillment->id,
			"school_year" => $updatedPayload["schoolYear"],
			"control_date" => $updatedPayload["controlDate"],
			"fulfillment_form" => $updatedPayload["fulfillmentForm"],
			"level" => $updatedPayload["level"],
			"relationship" => $updatedPayload["relationship"]
		]);
	}

	public function test_can_delete_compulsory_education_fulfillment()
	{
		$this->actingUser();
		$childrenRegistry = ChildrenRegistry::create([
			"school_unit_id" => SchoolUnit::factory()->create()->id,
		]);
		$student = Student::factory()->create([
			"children_registry_id" => $childrenRegistry->id,
		]);
		$fulfillment = CompulsoryEducationFulfillment::factory()->recycle($student)->create([
			"children_registry_id" => $childrenRegistry->id,
		]);
		$response = $this->delete("/api/childrenRegistry/$childrenRegistry->id/$student->id/fulfillment/$fulfillment->id");
		$response->assertOk();
		$this->assertDatabaseMissing("compulsory_education_fulfillments", [
			"id" => $fulfillment->id,
			"school_year" => $fulfillment->school_year,
			"control_date" => $fulfillment->control_date,
			"fulfillment_form" => $fulfillment->fulfillment_form,
			"level" => $fulfillment->level,
			"relationship" => $fulfillment->relationship
		]);
	}
}
