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
		$this->actingAdminUser();
		$child = Child::factory()->create();
		$payload = [
			"schoolYear" => 2025,
			"controlDate" => Carbon::now()->format("Y-m-d"),
			"kindergartenInfo" => "Przedszkole nr 1",
			"postponementInfo" => "Informacje o odroczeniu",
			"schoolInfo" => "Informacje o szkole, w tym o szkole za granicą lub przy przedstawicielstwie dyplomatycznym innego państwa w Polsce",
			"outOfSchoolInfo" => "Informacje o spełnianiu przez dziecko obowiązku szkolnego poza szkołą",
			"level" => 5
		];
		$response = $this->post("/api/children/$child->id/fulfillment", $payload);
		$response->assertCreated();
		$this->assertDatabaseHas("compulsory_education_fulfillments", [
			"child_id" => $child->id,
			"school_year" => $payload["schoolYear"],
			"control_date" => $payload["controlDate"],
			"kindergarten_info" => $payload["kindergartenInfo"],
			"postponement_info" => $payload["postponementInfo"],
			"school_info" => $payload["schoolInfo"],
			"out_of_school_info" => $payload["outOfSchoolInfo"],
			"level" => $payload["level"]
		]);
    }

	public function test_can_update_compulsory_education_fulfillment(): void
	{
		$this->actingAdminUser();
		$child = Child::factory()->create();
		$fulfillment = CompulsoryEducationFulfillment::factory()->recycle($child)->create();
		$updatedPayload = [
			"schoolYear" => 2026,
			"controlDate" => Carbon::now()->addYear()->format("Y-m-d"),
			"kindergartenInfo" => "Przedszkole nr 1",
			"postponementInfo" => "Informacje o odroczeniu",
			"schoolInfo" => "Informacje o szkole, w tym o szkole za granicą lub przy przedstawicielstwie dyplomatycznym innego państwa w Polsce",
			"outOfSchoolInfo" => "Informacje o spełnianiu przez dziecko obowiązku szkolnego poza szkołą",
			"level" => 2
		];
		$response = $this->put("/api/children/$child->id/fulfillment/$fulfillment->id", $updatedPayload);
		$response->assertOk();
		$this->assertDatabaseHas("compulsory_education_fulfillments", [
			"child_id" => $child->id,
			"school_year" => $updatedPayload["schoolYear"],
			"control_date" => $updatedPayload["controlDate"],
			"kindergarten_info" => $updatedPayload["kindergartenInfo"],
			"postponement_info" => $updatedPayload["postponementInfo"],
			"school_info" => $updatedPayload["schoolInfo"],
			"out_of_school_info" => $updatedPayload["outOfSchoolInfo"],
			"level" => $updatedPayload["level"]
		]);
	}

	public function test_can_delete_compulsory_education_fulfillment(): void
	{
		$this->actingAdminUser();
		$child = Child::factory()->create();
		$fulfillment = CompulsoryEducationFulfillment::factory()->recycle($child)->create();
		$response = $this->delete("/api/children/$child->id/fulfillment/$fulfillment->id");
		$response->assertOk();
		$this->assertDatabaseMissing("compulsory_education_fulfillments", [
			"id" => $fulfillment->id
		]);
	}
}
