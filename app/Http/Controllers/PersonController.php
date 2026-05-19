<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePersonRequest;
use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\Person;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Rules\Pesel;
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
			], 422);
		}

		$registryCheck = $this->checkIfRegistriesAreActive($validated);
		if ($registryCheck != null) {
			return $registryCheck;
		}

		try {
			$personId = $this->savePersonAndAddressToDatabase($validated, $schoolUnitId);
		} catch (Throwable $e) {
			Log::error($e);
			return response()->json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}

		return response()->json([
			"success" => true,
			"personId" => $personId
		], 201);
	}

	public function update(CreatePersonRequest $request, int $schoolUnitId, Person $person)
	{
		$registryCheck = $this->checkIfRegistriesAreActive($request->validated());
		if ($registryCheck != null) {
			return $registryCheck;
		}

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

		$registryCheck = $this->checkIfRegistriesAreActive($request->only(["studentRegistryId", "childrenRegistryId"]));
		if ($registryCheck != null) {
			return $registryCheck;
		}

		$file = $request->file("csvFile");
		$handle = fopen($file->getRealPath(), "r");
		if ($handle === false) {
			return response()->json(["success" => false, "errors" => ["CANNOT_READ_CSV"]], 500);
		}

		/* We can automatically detect if the file is using commas, semicolons or tabs as a delimiter by
		 reading the first line. Depending on locale settings, MS Excel can switch between commas and tabs.
		I don't think there is any spreadsheet software uses tabs by default, but TSV is frequently used by
		CKE, and it doesn't hurt to implement. */
		$firstLine = fgets($handle);
		if ($firstLine === false) {
			return response()->json(["success" => false, "errors" => ["EMPTY_FILE"]], 422);
		}
		if (str_contains($firstLine, "\t")) {
			$separator = "\t";
		} else if (str_contains($firstLine, ",")) {
			$separator = ",";
		} else {
			$separator = ";";
		}
		rewind($handle);

		$headers = fgetcsv($handle, 0, $separator);

		/* Strip UTF-8 BOM if present.
		MS Excel usually exports with a BOM at the beginning of the file, while other software (LibreOffice,
		Google Sheets, Apple Numbers) doesn't. */
		if (str_starts_with($headers[0], "\xEF\xBB\xBF")) {
			$headers[0] = substr($headers[0], 3);
		}
		$headers = array_map("trim", $headers);

		$rules = (new CreatePersonRequest())->rules();
		$rowsToInsert = [];
		$errors = [];
		$rowNumber = 2; // 1 is headers

		$seenPesels = [];
		$seenAltDocs = [];

		while (($data = fgetcsv($handle, 0, $separator)) !== false) {
			if (count($headers) !== count($data)) {
				$errors[] = "Rząd $rowNumber: Liczba kolumn nie zgadza się z wymaganą liczbą.";
				$rowNumber++;
				continue;
			}

			$rowData = array_combine($headers, $data);

			// treat empty spaces as nulls
			$rowData = array_map(function ($value) {
				$val = trim($value);
				return $val === "" ? null : $val;
			}, $rowData);

			if ($request->has("studentRegistryId")) {
				$rowData["studentRegistryId"] = $request->input("studentRegistryId");
			}
			if ($request->has("childrenRegistryId")) {
				$rowData["childrenRegistryId"] = $request->input("childrenRegistryId");
			}

			$validator = Validator::make($rowData, $rules);

			if ($validator->fails()) {
				foreach ($validator->errors()->all() as $errorMsg) {
					$errors[] = "Rząd $rowNumber: $errorMsg";
				}
				$rowNumber++;
				continue;
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
				$errors[] = "Rząd $rowNumber: Osoba o tym numerze PESEL / innym dokumencie identyfikacyjnym już istnieje.";
				$rowNumber++;
				continue;
			}

			$rowsToInsert[] = $validated;
			$rowNumber++;
		}
		fclose($handle);

		if (!empty($errors)) {
			return response()->json([
				"success" => false,
				"errors" => $errors
			], 422);
		}

		try {
			DB::transaction(function () use ($rowsToInsert, $schoolUnitId) {
				foreach ($rowsToInsert as $validatedRow) {
					$this->savePersonAndAddressToDatabase($validatedRow, $schoolUnitId);
				}
			});
		} catch (Throwable $e) {
			Log::error($e);
			return response()->json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}

		return response()->json([
			"success" => true,
			"importedCount" => count($rowsToInsert)
		], 201);
	}

	/**
	 * @throws Throwable
	 */
	private function savePersonAndAddressToDatabase(
		array   $validated,
		int     $schoolUnitId = null,
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

	private function checkIfRegistriesAreActive(array $validated): ?JsonResponse
	{
		if (isset($validated["studentRegistryId"])) {
			$studentRegistry = StudentRegistry::where("id", "=", $validated["studentRegistryId"])->first();
		}
		if (isset($validated["childrenRegistryId"])) {
			$childrenRegistry = ChildrenRegistry::where("id", "=", $validated["childrenRegistryId"])->first();
		}

		if ((isset($studentRegistry) && $studentRegistry->isArchived()) ||
			(isset($childrenRegistry) && $childrenRegistry->isArchived())) {
			return response()->json([
				"success" => false,
				"errors" => [
					"REGISTRY_ARCHIVED"
				]
			], 422);
		}
		return null;
	}
}
