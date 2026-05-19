<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\Person;
use App\Models\SchoolUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ChildControllerTest extends TestCase
{
	use RefreshDatabase;

	private function createChildrenRegistry(): ChildrenRegistry
	{
		$schoolUnit = SchoolUnit::factory()->create();
		return $schoolUnit->childrenRegistry()->create();
	}

	public function test_can_list_children(): void
	{
		$this->actingUser();
		$registry = $this->createChildrenRegistry();
		Child::factory(5)->recycle($registry)->create();
		$response = $this->get("/api/childrenRegistry/$registry->id");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonCount(5);
	}

	public function test_can_create_child(): void
	{
		$this->actingUser();
		$registry = $this->createChildrenRegistry();
		$person = Person::factory()->create([
			"school_unit_id" => $registry->schoolUnit->id
		]);
		$response = $this->post("/api/childrenRegistry/$registry->id", [
			"personId" => $person->id
		]);
		$response->assertCreated();
		$this->assertDatabaseHas("children", [
			"person_id" => $person->id,
			"children_registry_id" => $registry->id
		]);
	}

	public function test_cannot_create_child_without_valid_person_id(): void
	{
		$this->actingUser();
		$registry = $this->createChildrenRegistry();
		$response = $this->post("/api/childrenRegistry/$registry->id", [
			"personId" => 99999
		]);
		$response->assertUnprocessable();
	}

	public function test_can_show_child(): void
	{
		$this->actingUser();
		$child = Child::factory()->create();
		$response = $this->get("/api/children/$child->id");
		$response->assertOk();
	}

	public function test_can_delete_child(): void
	{
		$this->actingUser();
		$child = Child::factory()->create();
		$response = $this->delete("/api/children/$child->id");
		$response->assertOk();
		$child = $child->refresh();
		$this->assertTrue($child->trashed());
	}
}
