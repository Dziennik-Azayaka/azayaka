<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\CompulsoryEducationFulfillment;
use App\Models\SchoolUnit;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CompulsoryEducationFulfillmentControllerTest extends TestCase
{
	use RefreshDatabase;

    public function test_can_create_compulsory_education_fulfillment(): void
    {
		$this->actingUser();
		$child = Child::factory()->create();
		$payload = [
			"schoolYear" => 2025,
			"controlDate" => Carbon::now()->format("Y-m-d"),
			"fulfillmentForm" => "w szkole w której obwodzie mieszka uczeń",
			"level" => 5,
			"relationship" => "podlega obowiązku szkolnemu w szkole podstawowej"
		];
		$response = $this->post("/api/children/$child->id/fulfillment", $payload);
		$response->assertCreated();
		$this->assertDatabaseHas("compulsory_education_fulfillments", [
			"child_id" => $child->id,
			"school_year" => $payload["schoolYear"],
			"control_date" => $payload["controlDate"],
			"fulfillment_form" => $payload["fulfillmentForm"],
			"level" => $payload["level"],
			"relationship" => $payload["relationship"]
		]);
    }

	public function test_can_update_compulsory_education_fulfillment(): void
	{
		$this->actingUser();
		$child = Child::factory()->create();
		$fulfillment = CompulsoryEducationFulfillment::factory()->recycle($child)->create();
		$updatedPayload = [
			"schoolYear" => 2026,
			"controlDate" => Carbon::now()->addYear()->format("Y-m-d"),
			"fulfillmentForm" => "w szkole poza obwodem w którym mieszka uczeń",
			"level" => 2,
			"relationship" => "podlega obowiązkowi szkolnemu w szkole ponadpodstawowej"
		];
		$response = $this->put("/api/children/$child->id/fulfillment/$fulfillment->id", $updatedPayload);
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

	public function test_can_delete_compulsory_education_fulfillment(): void
	{
		$this->actingUser();
		$child = Child::factory()->create();
		$fulfillment = CompulsoryEducationFulfillment::factory()->recycle($child)->create();
		$response = $this->delete("/api/children/$child->id/fulfillment/$fulfillment->id");
		$response->assertOk();
		$this->assertDatabaseMissing("compulsory_education_fulfillments", [
			"id" => $fulfillment->id
		]);
	}
}
