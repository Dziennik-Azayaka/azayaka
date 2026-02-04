<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\AccountLog;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountAccessesControllerTest extends TestCase
{
	use RefreshDatabase;

	private function actingUser(): User
	{
		$user = User::factory()->create();
		$this->be($user);
		return $user;
	}

	public function test_lookup_success_and_not_found(): void
	{
		$student = Student::factory()->create([
			"first_name" => "Jan",
			"last_name" => "Kowalski",
		]);

		$access = AccountAccess::factory()->create([
			"student_id" => $student->id,
			"words" => "a,b,c",
		]);

		$response = $this->post("/api/activation/lookup", [
			"code" => "a,b,c",
		]);
		$response->assertOk();
		$response->assertJson([
			"success" => true,
			"firstName" => "Jan",
			"lastName" => "Kowalski",
		]);

		$response404 = $this->post("/api/activation/lookup", [
			"code" => "e,f,g",
		]);
		$response404->assertStatus(404);
		$response404->assertJson([
			"success" => false,
		]);
	}

	public function test_checkEmailAvailability_updates_session_and_reports_correctly(): void
	{
		$responseAvailable = $this->post("/api/activation/emailAvailability", [
			"email" => "free@example.com",
		]);
		$responseAvailable->assertOk();
		$responseAvailable->assertJson(["available" => true]);

		$user = User::factory()->create(["email" => "used@example.com"]);
		$responseTaken = $this->post("/api/activation/emailAvailability", [
			"email" => "used@example.com",
		]);
		$responseTaken->assertOk();
		$responseTaken->assertJson(["available" => false]);
	}

	public function test_createAccountOrAttachAccess_creates_new_user_and_logs_in_when_email_unused(): void
	{
		$student = Student::factory()->create(["first_name" => "Antoni", "last_name" => "Nowak"]);
		$code = "alpha,beta,gamma";
		$access = AccountAccess::factory()->create([
			"student_id" => $student->id,
			"words" => $code,
			"user_id" => null,
		]);

		$response = $this->post("/api/activation", [
			"code" => $code,
			"email" => "antoni.nowak@example.com",
			"password" => "password"
		]);

		$response->assertOk();
		$response->assertJson(["success" => true]);

		$this->assertAuthenticated();
		$user = auth()->user();
		$this->assertSame("antoni.nowak@example.com", $user->email);

		$access->refresh();
		$this->assertSame($user->id, $access->user_id);
		$this->assertNull($access->words);

		$this->assertDatabaseHas("account_logs", [
			"user_id" => $user->id,
			"event_type" => "successful_login_attempt",
		]);
	}

	public function test_createAccountOrAttachAccess_attaches_to_existing_user_with_correct_password_and_fails_with_wrong(): void
	{
		$student = Student::factory()->create(["first_name" => "Tadeusz", "last_name" => "Nowak"]);
		$code = "1,2,3";
		$access = AccountAccess::factory()->create([
			"student_id" => $student->id,
			"words" => $code,
			"user_id" => null,
		]);

		$existing = User::factory()->create([
			"email" => "tadeusz.nowak@example.com",
		]);

		$responseWrong = $this->post("/api/activation", [
			"code" => $code,
			"email" => "tadeusz.nowak@example.com",
			"password" => "incorrect123",
		]);
		$responseWrong->assertStatus(401);
		$responseWrong->assertJson([
			"success" => false,
		]);

		$responseOk = $this->post("/api/activation", [
			"code" => $code,
			"email" => "tadeusz.nowak@example.com",
			"password" => "password",
		]);
		$responseOk->assertOk();
		$responseOk->assertJson(["success" => true]);

		$this->assertAuthenticatedAs($existing->fresh());
		$access->refresh();
		$this->assertSame($existing->id, $access->user_id);
		$this->assertNull($access->words);
	}

	public function test_createAccountOrAttachAccess_returns_404_when_code_not_found(): void
	{
		$response = $this->post("/api/activation", [
			"code" => "not,a,real,code",
			"email" => "user@example.com",
			"password" => "password1234",
		]);
		$response->assertStatus(404);
		$response->assertJson(["success" => false]);
	}

	public function test_status_endpoint_reflects_session_state(): void
	{
		// Default state
		$responseDefault = $this->get("/api/activation/status");
		$responseDefault->assertOk();
		$responseDefault->assertJson(["step" => "not_started"]);

		// code_found
		$responseCode = $this->withSession([
			"activation_step" => "code_found",
			"activation_code" => "a,b,c",
		])->get("/api/activation/status");
		$responseCode->assertOk();
		$responseCode->assertJson([
			"step" => "code_found",
			"code" => "a,b,c",
		]);

		// email_available
		$responseEmailAvailable = $this->withSession([
			"activation_step" => "email_available",
			"activation_code" => "a,b,c",
			"activation_email" => "test@example.com",
		])->get("/api/activation/status");
		$responseEmailAvailable->assertOk();
		$responseEmailAvailable->assertJson([
			"step" => "email_available",
			"code" => "a,b,c",
			"email" => "test@example.com",
		]);

		// attach_to_account
		$responseAttach = $this->withSession([
			"activation_step" => "attach_to_account",
			"activation_code" => "a,b,c",
			"activation_email" => "test@example.com",
		])->get("/api/activation/status");
		$responseAttach->assertOk();
		$responseAttach->assertJson([
			"step" => "attach_to_account",
			"code" => "a,b,c",
			"email" => "test@example.com",
		]);
	}

	public function test_list_returns_accesses_with_personas_for_authenticated_user(): void
	{
		$user = $this->actingUser();

		// Student access
		$student = Student::factory()->create(["first_name" => "Krzysztof", "last_name" => "Nowak"]);
		$studentAccess = AccountAccess::factory()->create([
			"student_id" => $student->id,
			"employee_id" => null,
			"guardian_id" => null,
			"user_id" => $user->id,
		]);

		// Employee access
		$employee = Employee::factory()->create(["first_name" => "Adam", "last_name" => "Nowak"]);
		$employeeAccess = AccountAccess::factory()->create([
			"student_id" => null,
			"employee_id" => $employee->id,
			"guardian_id" => null,
			"user_id" => $user->id,
			"words" => "emp,code",
		]);

		// Guardian access with a linked student
		$guardian = Guardian::factory()->create(["first_name" => "Ewa", "last_name" => "Nowak"]);
		$student2 = Student::factory()->create(["first_name" => "Rozalia", "last_name" => "Nowak"]);
		$guardian->students()->attach($student2->id);
		$guardianAccess = AccountAccess::factory()->create([
			"student_id" => null,
			"employee_id" => null,
			"guardian_id" => $guardian->id,
			"user_id" => $user->id,
			"words" => "guard,code",
		]);

		$response = $this->get("/api/user");
		$response->assertOk();
		$json = $response->json();

		$this->assertSame($user->email, $json["email"]);
		$this->assertArrayHasKey("accesses", $json);
		$list = $json["accesses"];
		$this->assertIsArray($list);

		$this->assertGreaterThanOrEqual(3, count($list));

		$this->assertTrue(collect($list)->contains(fn($i) => $i["name"] === "Krzysztof Nowak"));
		$this->assertTrue(collect($list)->contains(fn($i) => $i["type"] === "guardian"));
	}
}
