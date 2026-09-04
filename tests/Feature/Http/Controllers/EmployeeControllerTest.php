<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class EmployeeControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_can_generate_employee_accesses_document(): void
	{
		$this->actingAdminUser();
		$employee = Employee::factory()->create();
		AccountAccess::factory()->create([
			"employee_id" => $employee->id,
			"words" => "a,b,c",
		]);

		$response = $this->post("/api/employees/accesses/document", [
			"ids" => [$employee->id],
		]);

		$response->assertOk();
		$response->assertHeader("Content-Type", "application/pdf");
		$this->assertMatchesRegularExpression(
			"/\.pdf/",
			$response->headers->get("Content-Disposition")
		);
	}

	public function test_cannot_generate_employee_accesses_document_without_words(): void
	{
		$this->actingAdminUser();
		$employee = Employee::factory()->create();

		$response = $this->post("/api/employees/accesses/document", [
			"ids" => [$employee->id],
		]);

		$response->assertStatus(422);
		$response->assertJsonFragment([
			"ENTITY_HAS_NO_ACCESS_WORDS",
		]);
	}

	public function test_generate_employee_accesses_document_validates_ids_presence(): void
	{
		$this->actingAdminUser();

		$response = $this->post("/api/employees/accesses/document", []);

		$response->assertUnprocessable();
	}
}
