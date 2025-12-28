<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\SchoolType;
use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolComplexControllerTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
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
		return $user;
    }

    public function test_list_returns_complexes_with_expected_fields(): void
    {
        $this->actingUser();
        SchoolComplex::factory()->count(2)->create();

        $response = $this->get("/api/schoolComplex");
        $response->assertOk();
        $response->assertJsonStructure([
            "*" => ["id", "name", "type"],
        ]);
    }

    public function test_create_without_existing_units_creates_complex_with_correct_type(): void
    {
        $this->actingUser();
        $response = $this->post("/api/schoolComplex", [
            "name" => "Zespół Szkół im. Dzienniczkowców",
        ]);

        $response->assertOk();
        $response->assertJson(["success" => true]);

        $this->assertDatabaseCount("school_complexes", 1);
        $complex = SchoolComplex::first();
        $this->assertNotNull($complex);
        $this->assertSame(SchoolType::ZESPOL_SZKOL_I_PLACOWEK_OSWIATOWYCH, $complex->type);
    }

    public function test_create_with_existing_units_assigns_parent_to_all_units(): void
    {
        $this->actingUser();
        $units = SchoolUnit::factory()->count(3)->create(["school_complex_id" => null]);

        $response = $this->post("/api/schoolComplex", [
            "name" => "Zespół Szkół im. Dzienniczkowców",
        ]);
        $response->assertOk();
        $response->assertJson(["success" => true]);

        $complex = SchoolComplex::first();
        $this->assertNotNull($complex);

        foreach ($units as $unit) {
            $unit->refresh();
            $this->assertSame($complex->id, $unit->school_complex_id);
        }
    }

    public function test_update_changes_name_and_sets_type(): void
    {
        $this->actingUser();
        $complex = SchoolComplex::factory()->create(["name" => "Old Name", "type" => SchoolType::LICEUM_OGOLNOKSZTALCACE->value]);

        $response = $this->put("/api/schoolComplex/{$complex->id}", [
            "name" => "Zespół Szkół im. Dzienniczkowców",
        ]);
        $response->assertOk();
        $response->assertJson(["success" => true]);

        $complex->refresh();
        $this->assertSame("Zespół Szkół im. Dzienniczkowców", $complex->name);
        $this->assertSame(SchoolType::ZESPOL_SZKOL_I_PLACOWEK_OSWIATOWYCH, $complex->type);
    }
}
