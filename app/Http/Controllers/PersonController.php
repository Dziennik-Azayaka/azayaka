<?php

namespace App\Http\Controllers;

use App\Exceptions\RegistryArchivedException;
use App\Http\Requests\CreatePersonRequest;
use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\Person;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Rules\Pesel;
use App\Utilities\CsvImportAssistant;
use Closure;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Log;
use Throwable;
use Validator;

class PersonController extends Controller
{
	public function lookup(Request $request, int $schoolUnitId)
	{
		$validated = $request->validate([
			"pesel" => ["required_without:alternateIdentityDocument", "prohibits:alternateIdentityDocument", "max:11", new Pesel],
			"alternateIdentityDocument" => ["required_without:pesel", "prohibits:pesel", "max:255"]
		]);

		$person = Person::where("school_unit_id", "=", $schoolUnitId);
		if (isset($validated["pesel"])) {
			$person = $person->where("pesel", $validated["pesel"]);
		} else {
			$person = $person->where("alternate_identity_document", $validated["alternateIdentityDocument"]);
		}
		$person = $person->first(["id", "first_name", "last_name", "second_name", "birthdate"]);
		if (isset($person)) {
			return [
				"found" => true,
				"id" => $person->id,
				"firstName" => $person->first_name,
				"lastName" => $person->last_name,
				"secondName" => $person->second_name,
				"birthdate" => $person->birthdate
			];
		} else {
			return [
				"found" => false
			];
		}
	}

	public function show(Person $person)
	{
		$personComplete = $person->load(["residenceAddress", "students", "children", "guardians"]);
		return [
			"person" => $personComplete->toResource(),
			"students" => $personComplete->students->map(fn($student) => $student->toResource()),
			"children" => $personComplete->children->map(fn($child) => $child->toResource())
		];
	}

	public function create(CreatePersonRequest $request, int $schoolUnitId)
	{
		$validated = $request->validated();
		if ((isset($validated["pesel"]) && Person::where("pesel", "=", $validated["pesel"])
					->where("school_unit_id", "=", $schoolUnitId)->exists()) ||
			(isset($validated["alternateIdentityDocument"]) &&
				Person::where("alternate_identity_document", "=", $validated["alternateIdentityDocument"])
					->where("school_unit_id", "=", $schoolUnitId)->exists())) {
			return response()->json([
				"success" => false,
				"errors" => [
					"PERSON_ALREADY_EXISTS"
				]
			], 409);
		}

		if (isset($validated["studentRegistryId"])) {
			$studentRegistry = StudentRegistry::where("id", "=", $validated["studentRegistryId"])->first();
			if (!isset($validated["studentRegistryNumber"])) {
				$validated["studentRegistryNumber"] = $studentRegistry->students()->max("student_registry_number") + 1;
			} else if ($studentRegistry->students()->where("student_registry_number", $validated["studentRegistryNumber"])->exists()) {
				return \Response::json([
					"success" => false,
					"errors" => [
						"STUDENT_REGISTRY_NUMBER_ALREADY_EXISTS"
					]
				], 409);
			}
		}

		$this->checkIfRegistriesAreActive($validated);

		$personId = $this->savePersonAndAddressToDatabase($validated, $schoolUnitId);

		return response()->json([
			"success" => true,
			"personId" => $personId
		], 201);
	}

	public function update(CreatePersonRequest $request, int $schoolUnitId, Person $person)
	{
		$this->checkIfRegistriesAreActive($request->validated());

		try {
			$this->savePersonAndAddressToDatabase($request->validated(), $schoolUnitId, $person);
		} catch (Throwable $e) {
			Log::error($e);
			return response()->json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}

		return [
			"success" => true
		];
	}

	public function destroy(Person $person)
	{
		$person->delete();
		return [
			"success" => true
		];
	}

	public function import(Request $request, int $schoolUnitId)
	{
		$request->validate([
			"csvFile" => ["required", "file", "mimes:csv,txt"],
			"studentRegistryId" => ["nullable", "exists:student_registries,id"],
			"childrenRegistryId" => ["nullable", "exists:children_registries,id"]
		]);

		$this->checkIfRegistriesAreActive($request->only(["studentRegistryId", "childrenRegistryId"]));

		// TODO: Better handle student registry numbers (rn we're blindly trusting the user)

		$rules = (new CreatePersonRequest())->rules();
		$seenPesels = [];
		$seenAltDocs = [];
		$rows = CsvImportAssistant::import($request->file("csvFile"),
			function (array $row, Closure $error, Closure $pass)
			use (&$seenAltDocs, &$seenPesels, $rules, $request, $schoolUnitId) {
				if ($request->has("studentRegistryId")) {
					$row["studentRegistryId"] = $request->input("studentRegistryId");
				}
				if ($request->has("childrenRegistryId")) {
					$row["childrenRegistryId"] = $request->input("childrenRegistryId");
				}

				$validator = Validator::make($row, $rules);

				if ($validator->fails()) {
					foreach ($validator->errors()->all() as $errorMsg) {
						$error($errorMsg);
					}
					return;
				}

				$validated = $validator->validated();

				$duplicate = false;
				if (isset($validated["pesel"])) {
					if (in_array($validated["pesel"], $seenPesels) ||
						Person::where("pesel", "=", $validated["pesel"])->where("school_unit_id", $schoolUnitId)->exists()) {
						$duplicate = true;
					} else {
						$seenPesels[] = $validated["pesel"];
					}
				} elseif (isset($validated["alternateIdentityDocument"])) {
					if (in_array($validated["alternateIdentityDocument"], $seenAltDocs) ||
						Person::where("alternate_identity_document", "=", $validated["alternateIdentityDocument"])
							->where("school_unit_id", $schoolUnitId)->exists()) {
						$duplicate = true;
					} else {
						$seenAltDocs[] = $validated["alternateIdentityDocument"];
					}
				}

				if ($duplicate) {
					$error("Osoba o tym numerze PESEL / innym dokumencie identyfikacyjnym już istnieje.");
					return;
				}

				$pass($validated);
			});

		DB::transaction(function () use ($rows, $schoolUnitId) {
			foreach ($rows as $row) {
				$this->savePersonAndAddressToDatabase($row, $schoolUnitId);
			}
		});

		return response()->json([
			"success" => true,
			"importedCount" => count($rows)
		], 201);
	}

	/**
	 * @throws Throwable
	 */
	private function savePersonAndAddressToDatabase(
		array   $validated,
		?int     $schoolUnitId = null,
		?Person $person = null): Person
	{
		$updating = !($person == null);
		if ($person == null) {
			$person = new Person();
		}

		DB::transaction(function () use ($validated, $schoolUnitId, $person, $updating) {
			$residenceAddress = new ResidenceAddress();
			$residenceAddress->country = $validated["residenceAddressCountry"];
			$residenceAddress->commune = $validated["residenceAddressCommune"] ?? null;
			$residenceAddress->town = $validated["residenceAddressTown"] ?? null;
			$residenceAddress->postal_code = $validated["residenceAddressPostalCode"] ?? null;
			$residenceAddress->house_number = $validated["residenceAddressHouseNumber"] ?? null;
			$residenceAddress->flat_number = $validated["residenceAddressFlatNumber"] ?? null;
			$residenceAddress->street = $validated["residenceAddressStreet"] ?? null;
			$residenceAddress->saveOrFail();

			$person->residence_address_id = $residenceAddress->id;
			$person->school_unit_id = $schoolUnitId;
			if (isset($validated["pesel"])) {
				$person->pesel = $validated["pesel"];
			} else {
				$person->alternate_identity_document = $validated["alternateIdentityDocument"];
			}
			$person->first_name = $validated["firstName"];
			$person->last_name = $validated["lastName"];
			if (isset($validated["secondName"])) {
				$person->second_name = $validated["secondName"];
			}
			$person->birthdate = $validated["birthdate"];
			$person->birthplace = $validated["birthplace"];
			if (isset($validated["gender"])) {
				$person->gender = $validated["gender"];
			}
			$person->saveOrFail();

			if (!$updating) {
				if (isset($validated["studentRegistryId"])) {
					$student = new Student();
					$student->student_registry_number = $validated["studentRegistryNumber"];
					$student->student_registry_id = $validated["studentRegistryId"];
					$student->admission_date = $validated["admissionDate"];
					$student->person_id = $person->id;
					$student->saveOrFail();
				}

				if (isset($validated["childrenRegistryId"])) {
					$child = new Child();
					$child->children_registry_id = $validated["childrenRegistryId"];
					$child->person_id = $person->id;
					$child->saveOrFail();
				}
			}
		});
		return $person;
	}

	/**
	 * @throws RegistryArchivedException
	 */
	private function checkIfRegistriesAreActive(array $validated)
	{
		if (isset($validated["studentRegistryId"])) {
			$studentRegistry = StudentRegistry::where("id", "=", $validated["studentRegistryId"])->first();
		}
		if (isset($validated["childrenRegistryId"])) {
			$childrenRegistry = ChildrenRegistry::where("id", "=", $validated["childrenRegistryId"])->first();
		}

		if ((isset($studentRegistry) && $studentRegistry->isArchived()) ||
			(isset($childrenRegistry) && $childrenRegistry->isArchived())) {
			throw new RegistryArchivedException();
		}
	}
}
