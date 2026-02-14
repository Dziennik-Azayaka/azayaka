<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\ChildrenRegistry;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Rules\Pesel;
use App\Utilities\ValidatorAssistant;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StudentController extends Controller
{
	public function list(StudentRegistry $studentRegistry)
	{
		return $studentRegistry->students()->with("residenceAddress")->get()->toResourceCollection();
	}

	public function create(Request $request, StudentRegistry $studentRegistry)
	{
		$validator = ValidatorAssistant::validate($request, $this->generateValidationRules(true, true));

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$student = $this->createAndSaveStudentWithResidenceAddress($validated);

		$studentRegistry->students()->attach($student);

		if ($validated["childrenRegistryId"] != null) {
			ChildrenRegistry::find($validated["childrenRegistryId"])->students()->attach($student);
		}

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(Request $request, StudentRegistry $studentRegistry, Student $student)
	{
		$validator = ValidatorAssistant::validate($request, $this->generateValidationRules(
			false, false, $student
		));

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];
		$student["first_name"] = $validated["firstName"];
		$student["last_name"] = $validated["lastName"];
		$student["second_name"] = $validated["secondName"] ?? null;
		$student["pesel"] = $validated["pesel"] ?? null;
		$student["alternate_identity_document"] = $validated["alternateIdentityDocument"] ?? null;
		$student["birthdate"] = $validated["birthdate"];
		$student["birthplace"] = $validated["birthplace"];
		$student["gender"] = $validated["gender"];
		$student["admission_date"] = $validated["admissionDate"];
		$student->save();
		return [
			"success" => true
		];
	}

	public function massCreateFromCSV(Request $request, StudentRegistry $studentRegistry)
	{
		$validator = ValidatorAssistant::validate($request, [
			"csv" => ["required", "file", File::types(["csv"])->max(2048)],
			"childrenRegistryId" => ["nullable", "exists:children_registries,id"],
			"delimiter" => ["nullable", "string"]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$childrenRegistryId = $validator["data"]["childrenRegistryId"];

		$lines = explode(PHP_EOL, trim($request->file("csv")->get()));
		$headers = str_getcsv(array_shift($lines));

		$uploadedData = array_map(function($line) use ($headers) {
			return array_combine($headers, str_getcsv($line));
		}, $lines);

		$validationRules = $this->generateValidationRules(true);

		DB::beginTransaction();
		$transactionSuccessful = true;

		$students = [];
		foreach ($uploadedData as $row) {
			$validator = ValidatorAssistant::validate($row, $validationRules);

			// TODO: Include information about the row which contains the error
			if (!$validator["success"]) {
				return $validator["errorResponse"];
			}
			$data = $validator["data"];

			// This isn't efficient; however, we need to know the IDs of addresses and students
			// to build relationships with the registries.
			$students[] = $this->createAndSaveStudentWithResidenceAddress($data, $transactionSuccessful);
		}

		$studentRegistry->students()->attach($students);
		if (array_key_exists("childrenRegistryId", $validator["data"])) {
			ChildrenRegistry::find($childrenRegistryId)->students()->attach($students);
		}

		if (!$transactionSuccessful) {
			DB::rollBack();
			return \Response::json([
				"success" => true,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		} else {
			DB::commit();
		}

		return \Response::json([
			"success" => true
		], 201);
	}

	private function generateValidationRules(bool $validateResidence = false, bool $validateChildrenRegistryId = false, ?Student $student = null)
	{
		$validationRules = ["firstName" => ["required", "max:255"],
			"lastName" => ["required", "max:255"],
			"secondName" => ["nullable", "max:255"],
			"pesel" => [
				"required_without:alternateIdentityDocument",
				$student != null ? Rule::unique("students")->ignore($student->id) : "unique:students",
				new Pesel
			],
			"alternateIdentityDocument" => [
				"required_without:pesel",
				"max:255",
				$student != null ?
					Rule::unique("students", "alternate_identity_document")->ignore($student->id) :
					"unique:students,alternate_identity_document"
			],
			"birthdate" => ["required", "date"],
			"birthplace" => ["required", "max:255"],
			"gender" => ["required", "in:male,female"],
			"admissionDate" => ["required", "date"],
		];

		if ($validateResidence) {
			$validationRules["residenceAddressCountry"] = ["required", "max:255"];
			$validationRules = array_merge($validationRules, array_fill_keys([
				"residenceAddressCommune",
				"residenceAddressTown",
				"residenceAddressPostalCode",
				"residenceAddressHouseNumber",
				"residenceAddressStreet"
			], ["nullable", "max:255"]));
		}

		if ($validateChildrenRegistryId) {
			$validationRules["childrenRegistryId"] = ["nullable", "exists:children_registries,id"];
		}

		return $validationRules;
	}

	private function createAndSaveStudentWithResidenceAddress(array $data, ?bool &$transactionStatus = null)
	{
		$residenceAddress = new ResidenceAddress();
		$residenceAddress->country = $data["residenceAddressCountry"];
		$residenceAddress->commune = $data["residenceAddressCommune"];
		$residenceAddress->town = $data["residenceAddressTown"];
		$residenceAddress->postal_code = $data["residenceAddressPostalCode"];
		$residenceAddress->house_number = $data["residenceAddressHouseNumber"];
		$residenceAddress->street = $data["residenceAddressStreet"];
		$residenceAddress->save();

		$student = new Student();
		$student->first_name = $data["firstName"];
		$student->last_name = $data["lastName"];
		$student->second_name = $data["secondName"] ?? null;
		$student->pesel = $data["pesel"] ?? null;
		$student->alternate_identity_document = $data["alternateIdentityDocument"] ?? null;
		$student->birthdate = $data["birthdate"];
		$student->birthplace = $data["birthplace"];
		$student->gender = $data["gender"];
		$student->admission_date = $data["admissionDate"];
		$student->residence_address_id = $residenceAddress->id;

		try {
			$student->saveOrFail();
		} catch (\Throwable) {
			// Saving the student failed, so we need to delete the residence address
			if ($transactionStatus != null) {
				$transactionStatus = false;
			}
			$residenceAddress->delete();
		}

		return $student;
	}
}
