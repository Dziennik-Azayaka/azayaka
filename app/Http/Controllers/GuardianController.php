<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
use App\Http\Requests\GuardianRequest;
use App\Models\ChildrenRegistry;
use App\Models\Guardian;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Utilities\CaseConverter;
use App\Utilities\ValidatorAssistant\ValidatorAssistantException;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
	public function list(Student $student) {
		return $student->guardians()->get()->toresourceCollection();
	}

	public function create(GuardianRequest $request, Student $student) {
		$this->checkIfRegistriesAreActive($student->studentRegistry, $student->childrenRegistry);
		$validated = CaseConverter::toSnakeCase($request->validated());
		$guardian = new Guardian($validated);
		$guardian->student()->associate($student);
		$residenceAddress = $this->saveResidenceAddress($validated);
		$guardian->residenceAddress()->associate($residenceAddress);
		$guardian->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(GuardianRequest $request, Guardian $guardian) {
		$this->checkIfRegistriesAreActive($guardian->student->studentRegistry, $guardian->student->childrenRegistry);
		$validated = CaseConverter::toSnakeCase($request->validated());
		$guardian->update($validated);
		$this->saveResidenceAddress($validated, $guardian->residenceAddress);
		return \Response::json([
			"success" => true
		]);
	}

	public function destroy(Guardian $guardian) {
		$this->checkIfRegistriesAreActive($guardian->student->studentRegistry, $guardian->student->childrenRegistry);
		$guardian->residenceAddress->delete();
		$guardian->delete();
		return \Response::json([
			"success" => true
		]);
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

	private function checkIfRegistriesAreActive(StudentRegistry $studentRegistry, ?ChildrenRegistry $childrenRegistry): void
	{
		if ($studentRegistry->isArchived() || $childrenRegistry?->isArchived()) {
			throw CustomValidationException::withMessages(["STUDENT_REGISTRY_OR_CHILDREN_REGISTRY_ARCHIVED"]);
		}
	}
}
