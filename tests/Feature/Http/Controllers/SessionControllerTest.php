<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post("/api/login", [
            "email" => $user->email,
            "password" => "password",
        ]);

        $response->assertOk();
        $response->assertJson(["success" => true]);
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $response = $this->post("/api/login", [
            "email" => $user->email,
            "password" => "wrong-password",
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            "success" => false,
            "errors" => ["INVALID_USERNAME_OR_PASSWORD"],
        ]);
        $this->assertGuest();
    }

    public function test_login_validation_errors_are_reported(): void
    {
        $response = $this->post("/api/login", [
            "email" => "not-an-email",
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            "success" => false,
            "errors" => [
                "EMAIL_MUST_BE_AN_EMAIL",
                "PASSWORD_IS_REQUIRED",
            ],
        ]);
        $this->assertGuest();
    }

    public function test_session_info_when_logged_in_and_logged_out(): void
    {
        // logged out
        $response = $this->get("/api/session");
        $response->assertOk();
        $response->assertJson([
            "loggedIn" => false,
        ]);

        // logged in
        $user = User::factory()->create();
        $this->be($user);
        $response = $this->get("/api/session");
        $response->assertOk();
        $response->assertJson([
            "loggedIn" => true,
            "email" => $user->email,
            "name" => $user->name,
        ]);
    }

	public function test_logout_route_logs_out_and_redirects(): void
	{
		$user = User::factory()->create();
		$this->be($user);

		$response = $this->get("/api/logout");

		$response->assertRedirect("/authentication");
		$this->assertGuest();
	}
}
