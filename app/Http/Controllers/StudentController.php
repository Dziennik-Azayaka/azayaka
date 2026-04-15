<?php

namespace App\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Rules\Pesel;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use App\Utilities\ValidatorAssistant\ValidatorAssistantException;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StudentController extends Controller
{
	public function listByStudentRegistry(StudentRegistry $studentRegistry)
	{
		return $studentRegistry->students()->with(["residenceAddress", "compulsoryEducationFulfillment"])->get()->toResourceCollection();
	}

	public function listByChildrenRegistry(ChildrenRegistry $childrenRegistry)
	{
		return $childrenRegistry->students()->with(["residenceAddress", "compulsoryEducationFulfillment"])->get()->toResourceCollection();
	}

	public function show(Student $student)
	{
		return $student->load(["residenceAddress", "compulsoryEducationFulfillment"])->toResource();
	}

	public function create(Request $request, StudentRegistry $studentRegistry)
	{
		$validator = ValidatorAssistant::validate($request, $this->generateValidationRules(true, true));

		$childrenRegistry = ChildrenRegistry::find($validator["childrenRegistryId"]);
		$this->checkIfRegistriesAreActive($studentRegistry, $childrenRegistry);

		try {
			DB::transaction(function () use ($studentRegistry, $childrenRegistry, $validator) {
				$this->createAndSaveStudentWithResidenceAddress(
					$studentRegistry->id, $validator, $childrenRegistry->id
				);
			});
		} catch (\Throwable) {
			return \Response::json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(Request $request, Student $student)
	{
		$this->checkIfRegistriesAreActive($student->studentRegistry, $student->childrenRegistry);

		$validated = ValidatorAssistant::validate($request, $this->generateValidationRules(
			false, false, $student
		));

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

	public function destroy(Student $student)
	{
		$this->checkIfRegistriesAreActive($student->studentRegistry, $student->childrenRegistry);
		DB::beginTransaction();
		try {
			$student->residenceAddress->delete();
			$student->delete();
			DB::commit();
			return [
				"success" => true
			];
		} catch (\Throwable) {
			DB::rollBack();
			return \Response::json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}
	}

	public function massCreateFromCSV(Request $request, StudentRegistry $studentRegistry)
	{
		$validator = ValidatorAssistant::validate($request, [
			"csv" => ["required", "file", File::types(["csv"])->max(2048)],
			"childrenRegistryId" => ["nullable", "exists:children_registries,id"],
			"delimiter" => ["nullable", "string"]
		]);

		$childrenRegistry = ChildrenRegistry::find($validator["childrenRegistryId"]);
		$this->checkIfRegistriesAreActive($studentRegistry, $childrenRegistry);

		$lines = explode(PHP_EOL, trim($request->file("csv")->get()));
		$headers = str_getcsv(array_shift($lines));

		$uploadedData = array_map(function ($line) use ($headers) {
			return array_combine($headers, str_getcsv($line));
		}, $lines);

		$validationRules = $this->generateValidationRules(true);

		try {
			DB::transaction(function () use ($uploadedData, $validationRules, $studentRegistry, $childrenRegistry) {
				foreach ($uploadedData as $row) {
					// TODO: Include information about the row which contains the error
					$validated = ValidatorAssistant::validate($row, $validationRules);
					$this->createAndSaveStudentWithResidenceAddress(
						$studentRegistry->id, $validated, $childrenRegistry->id
					);
				}
			});
		} catch (\Throwable) {
			return \Response::json([
				"success" => true,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
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
				new Pesel
			],
			"alternateIdentityDocument" => [
				"required_without:pesel",
				"max:255"
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
				"residenceAddressFlatNumber",
				"residenceAddressStreet"
			], ["nullable", "max:255"]));
		}

		if ($validateChildrenRegistryId) {
			$validationRules["childrenRegistryId"] = ["nullable", "exists:children_registries,id"];
		}

		return $validationRules;
	}

	/**
	 * @throws \Throwable
	 */
	private function createAndSaveStudentWithResidenceAddress(
		int   $studentRegistryId,
		array $data,
		?int  $childrenRegistryId = null)
	{
		$residenceAddress = new ResidenceAddress();
		$residenceAddress->country = $data["residenceAddressCountry"];
		$residenceAddress->commune = $data["residenceAddressCommune"] ?? null;
		$residenceAddress->town = $data["residenceAddressTown"] ?? null;
		$residenceAddress->postal_code = $data["residenceAddressPostalCode"] ?? null;
		$residenceAddress->house_number = $data["residenceAddressHouseNumber"] ?? null;
		$residenceAddress->flat_number = $data["residenceAddressFlatNumber"] ?? null;
		$residenceAddress->street = $data["residenceAddressStreet"] ?? null;
		$residenceAddress->saveOrFail();

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
		$student->student_registry_id = $studentRegistryId;
		$student->children_registry_id = $childrenRegistryId;
		$student->saveOrFail();
		return $student;
	}

	private function checkIfRegistriesAreActive(StudentRegistry $studentRegistry, ?ChildrenRegistry $childrenRegistry)
	{
		if ($studentRegistry->isArchived() || $childrenRegistry?->isArchived()) {
			throw new ValidatorAssistantException(null, null, ["STUDENT_REGISTRY_OR_CHILDREN_REGISTRY_ARCHIVED"]);
		}
	}
}
