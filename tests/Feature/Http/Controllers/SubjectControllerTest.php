<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
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

	public function test_can_list_subjects()
	{
		$actingUser = $this->actingUser();
		Subject::factory()->count(10)->create();
		$response = $this->get("/api/subjects", ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonStructure([
			"*" => [
				"id",
				"name",
				"shortcut",
				"active"
			],
		]);
	}

	public function test_can_create_subject()
	{
		$actingUser = $this->actingUser();
		$response = $this->post("/api/subjects", [
			"name" => "Test Subject",
			"shortcut" => "TSubject"
		], ["Access-ID" => $actingUser["access"]]);

		$response->assertCreated();
		$this->assertDatabaseHas("subjects", ["name" => "Test Subject", "shortcut" => "TSubject"]);
	}

	public function test_creating_subject_with_non_unique_shortcut_fails()
	{
		Subject::factory()->create([
			"name" => "Colliding Subject",
			"shortcut" => "CSubject"
		]);

		$actingUser = $this->actingUser();
		$response = $this->post("/api/subjects", [
			"name" => "Colliding Subject 2",
			"shortcut" => "CSubject"
		], ["Access-ID" => $actingUser["access"]]);

		$response->assertBadRequest();
	}

	public function test_can_update_subject()
	{
		$actingUser = $this->actingUser();
		$subject = Subject::factory()->create([
			"name" => "Old Subject",
			"shortcut" => "OSubject"
		]);

		$response = $this->put("/api/subjects/{$subject->id}", [
			"name" => "New Subject",
			"shortcut" => "NSubject"
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$this->assertDatabaseHas("subjects", ["name" => "New Subject", "shortcut" => "NSubject"]);
		$this->assertDatabaseMissing("subjects", ["name" => "Old Subject", "shortcut" => "OSubject"]);
	}

	public function test_can_archive_subject()
	{
		$actingUser = $this->actingUser();
		$subject = Subject::factory()->create();
		$response = $this->put("/api/subjects/{$subject->id}/activity", [], ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$this->assertDatabaseHas("subjects", ["id" => $subject->id, "active" => false]);
	}

	public function test_can_unarchive_subject()
	{
		$actingUser = $this->actingUser();
		$subject = Subject::factory()->create([
			"active" => false
		]);
		$response = $this->put("/api/subjects/{$subject->id}/activity", [], ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$this->assertDatabaseHas("subjects", ["id" => $subject->id, "active" => true]);
	}
}
