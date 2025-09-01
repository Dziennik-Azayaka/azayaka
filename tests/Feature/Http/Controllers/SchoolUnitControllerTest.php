<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SchoolType;
use App\Enums\Voivodeship;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolUnitControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        $user = User::factory()->create();
        $this->be($user);
        return $user;
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            "name" => "Technikum nr 1",
            "type" => SchoolType::TECHNIKUM->value,
            "studentCategory" => "childrenAndYouths",
            "municipality" => "Łódź",
            "voivodeship" => Voivodeship::LODZKIE->value,
            "district" => "Bałuty",
            "address" => "ul. Dzienniczkowa 23",
            "shortName" => "TECH 1",
            "schoolComplexId" => null,
        ], $overrides);
    }

    public function test_list_returns_units_resource_collection(): void
    {
        $this->actingUser();
        // create few units
        SchoolUnit::factory()->count(2)->create(["school_complex_id" => null]);

        $response = $this->get("/api/schoolunits");
        $response->assertOk();
        $response->assertJsonIsArray();
        $response->assertJsonStructure([
            "*" => [
                "id",
                "name",
                "active",
                "type",
                "studentCategory",
                "municipality",
                "voivodeship",
                "district",
                "address",
                "shortName",
                "schoolComplexId",
            ],
        ]);
    }

    public function test_create_persists_valid_school_unit(): void
    {
        $this->actingUser();
        $complex = SchoolComplex::factory()->create();

        $payload = $this->validPayload(["schoolComplexId" => $complex->id]);
        $response = $this->post("/api/schoolunits", $payload);

        $response->assertOk();
        $response->assertJson(["success" => true]);
        $this->assertDatabaseHas("school_units", [
            "name" => $payload["name"],
            "type" => $payload["type"],
            "student_category" => $payload["studentCategory"],
            "municipality" => $payload["municipality"],
            "voivodeship" => $payload["voivodeship"],
            "district" => $payload["district"],
            "address" => $payload["address"],
            "short_name" => $payload["shortName"],
            "school_complex_id" => $complex->id,
        ]);
    }

    public function test_create_rejects_invalid_student_category(): void
    {
        $this->actingUser();
        $payload = $this->validPayload(["studentCategory" => "invalid-type"]);

        $response = $this->post("/api/schoolunits", $payload);
        $response->assertStatus(400);
        $response->assertJson([
            "success" => false,
            "errors" => ["INVALID_STUDENT_CATEGORY"],
        ]);
    }

    public function test_create_blocks_multiple_units_without_parent(): void
    {
        $this->actingUser();
        // create more than one existing unit
        SchoolUnit::factory()->count(2)->create(["school_complex_id" => null]);

        $payload = $this->validPayload(["schoolComplexId" => null]);
        $response = $this->post("/api/schoolunits", $payload);

        // Controller returns JSON without explicit error codes array structure besides the error code and no status, default 200
        $response->assertOk();
        $response->assertJson([
            "success" => false,
            "errors" => ["CANNOT_CREATE_MULTIPLE_SCHOOL_UNITS_WITHOUT_PARENT"],
        ]);
    }

    public function test_archive_toggles_active_and_blocks_update_when_inactive(): void
    {
        $this->actingUser();
        $complex = SchoolComplex::factory()->create();
        $unit = SchoolUnit::factory()->create(["school_complex_id" => $complex->id]);

        // Archive (set inactive)
        $response = $this->put("/api/schoolunits/{$unit->id}/activity", [
            "password" => "password", // current_password rule
            "state" => false,
        ]);
        $response->assertOk();
        $response->assertJson(["success" => true]);
        $this->assertDatabaseHas("school_units", [
            "id" => $unit->id,
            "active" => false,
        ]);

        // Try to update inactive unit
        $updatePayload = $this->validPayload(["name" => "Updated Name", "schoolComplexId" => $complex->id]);
        $updateResponse = $this->put("/api/schoolunits/{$unit->id}", $updatePayload);
        $updateResponse->assertOk();
        $updateResponse->assertJson([
            "success" => false,
            "errors" => ["SCHOOL_UNIT_NOT_ACTIVE"],
        ]);

        // Unarchive (set active)
        $response = $this->put("/api/schoolunits/{$unit->id}/activity", [
            "password" => "password",
            "state" => true,
        ]);
        $response->assertOk();
        $this->assertDatabaseHas("school_units", [
            "id" => $unit->id,
            "active" => true,
        ]);

        // Now update should succeed
        $updateResponse = $this->put("/api/schoolunits/{$unit->id}", $updatePayload);
        $updateResponse->assertOk();
        $updateResponse->assertJson(["success" => true]);
        $this->assertDatabaseHas("school_units", [
            "id" => $unit->id,
            "name" => "Updated Name",
        ]);
    }
}
