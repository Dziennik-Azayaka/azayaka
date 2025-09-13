<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\AccountEventType;
use App\Models\AccountLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class AccountLogControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $this->be($user);
        return $user;
    }

    public function test_list_returns_paginated_camelised_logs_for_authenticated_user(): void
    {
        $user = $this->actingUser();
        AccountLog::factory()->count(60)->create([
            "user_id" => $user->id,
            "created_at" => now(),
        ]);

        $response = $this->get("/api/user/logs");
        $response->assertOk();
        $json = $response->json();

        $this->assertArrayHasKey("currentPage", $json);
        $this->assertArrayHasKey("data", $json);
        $this->assertIsArray($json["data"]);
        $this->assertCount(50, $json["data"]);

        $first = $json["data"][0];
        $this->assertArrayHasKey("eventType", $first);
        $this->assertArrayHasKey("ip", $first);
        $this->assertArrayHasKey("userAgent", $first);
        $this->assertArrayHasKey("createdAt", $first);

        $responsePage2 = $this->get("/api/user/logs?page=2");
        $responsePage2->assertOk();
        $json2 = $responsePage2->json();
        $this->assertArrayHasKey("data", $json2);
        $this->assertCount(10, $json2["data"]);
    }

    public function test_getDateOfLastUpdateToCredentials_returns_earliest_date_or_null(): void
    {
        $user = $this->actingUser();

        $earlier = now()->subDays(3);
        $later = now()->subDay();
        AccountLog::factory()->create([
            "user_id" => $user->id,
            "event_type" => AccountEventType::CREDENTIALS_CHANGED->value,
            "created_at" => $earlier,
        ]);
        AccountLog::factory()->create([
            "user_id" => $user->id,
            "event_type" => AccountEventType::CREDENTIALS_CHANGED->value,
            "created_at" => $later,
        ]);

        $response = $this->get("/api/user/logs/lastCredentialUpdate");
        $response->assertOk();
        $response->assertJson([
            "success" => true,
        ]);
        $date = $response->json("date");
        $this->assertNotNull($date);
        $this->assertSame($earlier->toIso8601String(), Carbon::parse($date)->toIso8601String());

        // Different user with no credentials_changed
        $this->be(User::factory()->create());
        $response2 = $this->get("/api/user/logs/lastCredentialUpdate");
        $response2->assertOk();
        $response2->assertJson([
            "success" => true,
            "date" => null,
        ]);
    }
}
