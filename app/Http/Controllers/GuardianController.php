<?php

namespace App\Http\Controllers;

use App\Documents\AccountAccessesActivation\AccountAccessesActivationDocument;
use App\Enums\AccessType;
use App\Exceptions\CustomValidationException;
use App\Http\Requests\GuardianRequest;
use App\Models\AccountAccess;
use App\Models\ChildrenRegistry;
use App\Models\Guardian;
use App\Models\Person;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Utilities\AccountAccessDocumentGenerator;
use App\Utilities\AccountAccessWordsGenerator;
use App\Utilities\CaseConverter;
use App\Utilities\ValidatorAssistant\ValidatorAssistantException;
use Illuminate\Http\Request;
use Response;

class GuardianController extends Controller
{
	public function create(GuardianRequest $request, Person $person)
	{
		$this->checkIfSchoolUnitIsActive($person);

		$validated = CaseConverter::toSnakeCase($request->validated());
		$guardian = new Guardian($validated);
		$guardian->person()->associate($person);
		$residenceAddress = $this->saveResidenceAddress($validated);
		$guardian->residenceAddress()->associate($residenceAddress);
		$guardian->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(GuardianRequest $request, Guardian $guardian)
	{
		$this->checkIfSchoolUnitIsActive($guardian->person);
		$validated = CaseConverter::toSnakeCase($request->validated());
		$guardian->update($validated);
		$this->saveResidenceAddress($validated, $guardian->residenceAddress);
		return \Response::json([
			"success" => true
		]);
	}

	public function destroy(Guardian $guardian)
	{
		$this->checkIfSchoolUnitIsActive($guardian->person);
		$guardian->residenceAddress->delete();
		$guardian->delete();
		return \Response::json([
			"success" => true
		]);
	}

	public function generateOrRegenerateAccess(Guardian $guardian, Student $student)
	{
		AccountAccess::where("guardian_id", $guardian->id)
			->where("student_id", $student->id)->delete();
		$accountAccess = new AccountAccess();
		$accountAccess->guardian_id = $guardian->id;
		$accountAccess->student_id = $student->id;
		$accountAccess->words = AccountAccessWordsGenerator::generate();
		$accountAccess->save();
		return [
			"success" => true,
			"words" => $accountAccess->words
		];
	}

	public function listAccesses(Request $request)
	{
		$guardians = Guardian::query();
		if ($request->has("classUnitId")) {
			$guardians->whereHas("person", function ($personQuery) use ($request) {
				$personQuery->whereHas("students", function ($studentQuery) use ($request) {
					$studentQuery->whereHas("gradebooks", function ($gradebookQuery) use ($request) {
						$gradebookQuery->where("class_unit_id", "=", $request->input("classUnitId"));
					});
				});
			});
		}

		return $guardians->with(["accountAccesses", "person"])->get()->map(fn($guardian) => [
			"guardianFirstName" => $guardian->first_name,
			"guardianSecondName" => $guardian->second_name,
			"guardianLastName" => $guardian->last_name,
			"guardianId" => $guardian->id,
			"students" => $guardian->person->students->map(fn($student) => [
				"studentId" => $student->id,
				"firstName" => $student->person->first_name,
				"secondName" => $student->person->second_name,
				"lastName" => $student->person->last_name
			]),
			"accessWords" => $guardian->accountAccesses->filter(function ($access) {
				return $access->guardian_id != null && $access->student_id != null;
			})->first()
		]);
	}

	public function generateAccessesDocument(Request $request)
	{
		$validatedData = $request->validate([
			"ids" => "required|array"
		]);

		$generator = new AccountAccessDocumentGenerator(AccessType::PARENT, $validatedData["ids"]);
		return $generator->generateDocument();
	}

	private function saveResidenceAddress(array $data, ?ResidenceAddress $residenceAddress = null): ResidenceAddress
	{
		if ($residenceAddress == null) $residenceAddress = new ResidenceAddress();
		$residenceAddress->country = $data["residence_address_country"];
		$residenceAddress->commune = $data["residence_address_commune"] ?? null;
		$residenceAddress->town = $data["residence_address_town"] ?? null;
		$residenceAddress->postal_code = $data["residence_address_postal_code"] ?? null;
		$residenceAddress->street = $data["residence_address_street"] ?? null;
		$residenceAddress->house_number = $data["residence_address_house_number"] ?? null;
		$residenceAddress->flat_number = $data["residence_address_flat_number"] ?? null;
		$residenceAddress->save();
		return $residenceAddress;
	}

	private function checkIfSchoolUnitIsActive(Person $person)
	{
		if (!$person->schoolUnit->active) {
			throw CustomValidationException::withMessages(["SCHOOL_UNIT_NOT_ACTIVE"]);
		}
	}
}
