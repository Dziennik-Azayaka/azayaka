<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class GuardianControllerTest extends TestCase
{
	public function test_can_list_guardians(): void
	{
		$this->actingUser();
		$student = Student::factory()->create();
		Guardian::factory()->count(5)->create([
			"student_id" => $student->id
		]);
		$response = $this->get("/api/students/$student->id/guardians");
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonCount(5);
		$response->assertJsonStructure([
			"*" => [
				"id",
				"firstName",
				"lastName",
				"email",
				"phoneNumber",
			]
		]);
	}
}
