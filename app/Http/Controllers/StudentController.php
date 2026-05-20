<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomValidationException;
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
	public function list(StudentRegistry $studentRegistry, Request $request)
	{
		$query = $studentRegistry->students()->with(["person", "person.residenceAddress", "person.guardians"]);

		if ($request->has("birthYear")) {
			$query = $query->whereHas("person", function ($personQuery) use ($request) {
				$personQuery->whereYear("birthdate", "=", $request->input("birthYear"));
			});
		}

		if ($request->has("gender")) {
			$query = $query->whereHas("person", function ($personQuery) use ($request) {
				$personQuery->where("gender", "=", $request->input("gender"));
			});
		}

		if ($request->has("status")) {
			switch ($request->input("status")) {
				case "active":
					$query = $query->whereNull("leave_date");
					break;
				case "inactive":
					$query = $query->whereNotNull("leave_date");
					break;
				case "trashed":
					$query = $query->onlyTrashed();
					break;
			}
		}

		if ($request->has("classUnitId")) {
			$query = $query->whereHas("classUnits", function ($classUnitQuery) use ($request) {
				$classUnitQuery->where("class_units.id", "=", $request->input("classUnitId"));
			});
		} else if ($request->has("level")) {
			$query = $query->whereHas("classUnits", function ($classUnitQuery) use ($request) {
				$classUnitQuery->whereHas("periods", function ($periodQuery) use ($request) {
					$now = now();

					$periodQuery->where("period_start", "<=", $now)
						->where("period_end", ">=", $now)
						->where("class_units_periods.level", "=", $request->input("level"));
				});
			});
		}

		return $query->get()->toResourceCollection();
	}

	public function show(Student $student)
	{
		return $student->load(["person", "person.residenceAddress"])->toResource();
	}

	public function create(Request $request, StudentRegistry $studentRegistry)
	{
		$validated = $request->validate([
			"personId" => ["required", "exists:people,id"],
			"admissionDate" => ["required", "date"]
		]);

		$student = new Student();
		$student->person_id = $validated["personId"];
		$student->student_registry_id = $studentRegistry->id;
		$student->admission_date = $validated["admissionDate"];
		$student->saveOrFail();

		return \Response::json(["success" => true,
			"studentId" => $student->id,], 201);
	}

	public function update(Request $request, Student $student)
	{
		$this->checkIfRegistryIsActive($student->studentRegistry);

		$validated = $request->validate([
			"admissionDate" => ["required", "date"],
			"leaveDate" => ["nullable", "date"],
			"leaveReason" => ["nullable", "string", "max:255"],
		]);

		$student->admission_date = $validated["admissionDate"];
		$student->leave_date = $validated["leaveDate"] ?? null;
		$student->leave_reason = $validated["leaveReason"] ?? null;

		$student->save();
		return [
			"success" => true
		];
	}

	public function destroy(Student $student)
	{
		$this->checkIfRegistryIsActive($student->studentRegistry);
		$student->delete();
		return [
			"success" => true
		];
	}

	private function checkIfRegistryIsActive(StudentRegistry $studentRegistry)
	{
		if ($studentRegistry->isArchived()) {
			throw CustomValidationException::withMessages(["STUDENT_REGISTRY_ARCHIVED"]);
		}
	}
}
