<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class UserPreferenceControllerTest extends TestCase
{
	use RefreshDatabase;

	public function test_list_returns_preferences_for_authenticated_user(): void
	{
		$user = $this->actingAdminUser();

		UserPreference::create([
			"user_id" => $user->id,
			"key" => "autofillAttendance",
			"value" => "1",
		]);

		$response = $this->getJson("/api/user/preferences");
		$response->assertOk();
		$response->assertJson([
			"autofillAttendance" => "1",
		]);
	}

	public function test_list_only_returns_authenticated_users_preferences(): void
	{
		$user = $this->actingAdminUser();

		UserPreference::create([
			"user_id" => $user->id,
			"key" => "autofillAttendance",
			"value" => "1",
		]);

		$otherUser = \App\Models\User::factory()->create();
		UserPreference::create([
			"user_id" => $otherUser->id,
			"key" => "autofillAttendance",
			"value" => "0",
		]);

		$response = $this->getJson("/api/user/preferences");
		$response->assertOk();
		$response->assertJsonCount(1);
		$response->assertJson([
			"autofillAttendance" => "1",
		]);
	}

	public function test_update_returns_404_for_invalid_key(): void
	{
		$this->actingAdminUser();

		$response = $this->putJson("/api/user/preferences/abcd", [
			"value" => "aaa",
		]);
		$response->assertStatus(404);
		$response->assertJson(["success" => false]);
	}

	public function test_can_update_preference(): void
	{
		$this->actingAdminUser();

		$response = $this->putJson("/api/user/preferences/autofillAttendance", [
			"value" => "1",
		]);
		$response->assertStatus(200);
		$response->assertJson(["success" => true]);
	}
}
