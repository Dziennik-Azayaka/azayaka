<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class StudentControllerTest extends TestCase
{
	use RefreshDatabase;

	private function actingUser(): array
	{
		$user = User::factory()->create();
		$this->be($user);
		$employee = Employee::factory()->create([
			"is_admin" => true,
			"is_headmaster" => true
		]);
		$accountAccess = AccountAccess::create();
		$accountAccess->employee_id = $employee->id;
		$accountAccess->user_id = $user->id;
		$accountAccess->save();
		return ["user" => $user, "access" => $accountAccess->id];
	}

	private function generateStudentRegistry() {
		$schoolComplex = SchoolComplex::factory()->create();
		$schoolUnit = SchoolUnit::factory()->create([
			"school_complex_id" => $schoolComplex->id
		]);
		return $schoolUnit->studentRegistry()->create();
	}

    public function test_can_list_students() {
		$actingUser = $this->actingUser();
		$registry = $this->generateStudentRegistry();
		$students = Student::factory()->count(5)->create();
		$registry->students()->attach($students);
		$response = $this->get("/api/studentRegistry/$registry->id", ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$response->assertJsonIsArray();
		$response->assertJsonCount(5);
	}

	public function test_can_create_students() {
		$actingUser = $this->actingUser();
		$studentRegistry = $this->generateStudentRegistry();
		$childrenRegistry = $studentRegistry->schoolUnit->childrenRegistry()->create();
		$response = $this->post("/api/studentRegistry/$studentRegistry->id", [
			"firstName" => "Grzegorz",
			"lastName" => "Brzęczyszcykiewicz",
			"pesel" => "55082669838",
			"birthdate" => "1978-01-02",
			"birthplace" => "Łękołody",
			"gender" => "male",
			"admissionDate" => "2021-01-01",
			"residenceAddressCountry" => "PL",
			"residenceAddressCommune" => "Łękołody",
			"residenceAddressTown" => "Chrząszczyżewoszyce",
			"residenceAddressPostalCode" => "69-420",
			"residenceAddressHouseNumber" => "3",
			"residenceAddressStreet" => "Szeroka",
			"childrenRegistryId" => $childrenRegistry->id
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertCreated();
		$this->assertDatabaseHas("students", [
			"first_name" => "Grzegorz",
			"last_name" => "Brzęczyszcykiewicz",
			"pesel" => "55082669838",
			"alternate_identity_document" => null,
			"birthdate" => "1978-01-02",
			"birthplace" => "Łękołody",
			"gender" => "male",
			"admission_date" => "2021-01-01"
		]);
		$this->assertDatabaseHas("residence_addresses", [
			"country" => "PL",
			"commune" => "Łękołody",
			"town" => "Chrząszczyżewoszyce",
			"postal_code" => "69-420",
			"house_number" => "3",
			"street" => "Szeroka"
		]);

		$student = Student::where("first_name", "Grzegorz")->first();
		$this->assertTrue($childrenRegistry->students->contains($student));
		$this->assertTrue($studentRegistry->students->contains($student));
	}

	public function test_can_update_students() {
		$actingUser = $this->actingUser();
		$studentRegistry = $this->generateStudentRegistry();
		$student = Student::factory()->create([
			"pesel" => "987654321",
			"alternate_identity_document" => null
		]);
		$studentRegistry->students()->attach($student);
		$response = $this->put("/api/studentRegistry/$studentRegistry->id/$student->id", [
			"firstName" => "Jan",
			"lastName" => "Nowak",
			"birthdate" => "1999-01-01",
			"birthplace" => "Warszawa",
			"gender" => "male",
			"admissionDate" => "2021-01-01",
			"pesel" => null,
			"alternateIdentityDocument" => "123456789"
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertOk();
		$this->assertDatabaseHas("students", [
			"id" => $student->id,
			"first_name" => "Jan",
			"last_name" => "Nowak",
			"birthdate" => "1999-01-01",
			"birthplace" => "Warszawa",
			"gender" => "male",
			"admission_date" => "2021-01-01",
			"pesel" => null,
			"alternate_identity_document" => "123456789"
		]);
	}

	public function test_can_mass_create_students() {
		$actingUser = $this->actingUser();
		$studentRegistry = $this->generateStudentRegistry();
		$childrenRegistry = $studentRegistry->schoolUnit->childrenRegistry()->create();
		$response = $this->post("/api/studentRegistry/$studentRegistry->id/massCreate", [
			"childrenRegistryId" => $childrenRegistry->id,
			"csv" => new UploadedFile(storage_path("tests/StudentControllerMassInsert.csv"), "upload.csv", null, null, true)
		], ["Access-ID" => $actingUser["access"]]);
		$response->assertCreated();
		$this->assertDatabaseCount("students", 2);
		$this->assertDatabaseCount("residence_addresses", 2);
		$this->assertDatabaseHas("students", ["first_name" => "Grzegorz", "birthdate" => "1978-01-02"]);
	}
}
