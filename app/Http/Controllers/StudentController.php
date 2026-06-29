<?php

namespace App\Http\Controllers;

use App\Documents\AccountAccessesActivation\AccountAccessesActivationDocument;
use App\Enums\AccessType;
use App\Exceptions\CustomValidationException;
use App\Exceptions\EntityAlreadyExistsException;
use App\Exceptions\RegistryArchivedException;
use App\Http\Resources\ResidenceAddressResource;
use App\Models\AccountAccess;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Utilities\AccountAccessDocumentGenerator;
use App\Utilities\AccountAccessWordsGenerator;
use Illuminate\Http\Request;
use Response;

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
			$query = $query->whereHas("gradebooks", function ($gradebookQuery) use ($request) {
				$gradebookQuery->where("class_unit_id", "=", $request->input("classUnitId"));
			});
		} else if ($request->has("level")) {
			$query = $query->whereHas("gradebooks", function ($gradebookQuery) use ($request) {
				$gradebookQuery->whereHas("startingClassificationPeriod", function ($periodQuery) use ($request) {
					$now = now();

					$periodQuery->where("period_start", "<=", $now)
						->where("period_end", ">=", $now)
						->where("class_units_periods.level", "=", $request->input("level"));
				});
			});
		}

		$sortableColumns = [
			"id" => "students.id",
			"person.lastName" => "people.last_name",
			"names" => "people.first_name",
			"birthdate" => "people.birthdate",
			"admissionDate" => "students.admission_date",
		];

		if ($request->has("sort") && isset($sortableColumns[$request->input("sort")])) {
			$sortColumn = $sortableColumns[$request->input("sort")];
			$sortDirection = $request->input("order", "asc") === "desc" ? "desc" : "asc";

			if (str_starts_with($sortColumn, "people.")) {
				$query->join("people", "students.person_id", "=", " people.id")
					->select("students.*");
			}

			$query->orderBy($sortColumn, $sortDirection);
		}

		return $query->paginate(100)->toResourceCollection();
	}


	public function create(Request $request, StudentRegistry $studentRegistry)
	{
		$validated = $request->validate([
			"personId" => ["required", "exists:people,id"],
			"admissionDate" => ["required", "date"],
			"studentRegistryNumber" => ["nullable", "integer"]
		]);

		if ($validated["studentRegistryNumber"] == null) {
			$validated["studentRegistryNumber"] = $studentRegistry->students()->max("student_registry_number") + 1;
		} else if ($studentRegistry->students()->where("student_registry_number", $validated["studentRegistryNumber"])->exists()) {
			throw new EntityAlreadyExistsException("STUDENT_REGISTRY_NUMBER");

		}

		$student = new Student();
		$student->person_id = $validated["personId"];
		$student->student_registry_id = $studentRegistry->id;
		$student->admission_date = $validated["admissionDate"];
		$student->student_registry_number = $validated["studentRegistryNumber"];
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

	public function generateOrRegenerateAccess(Student $student)
	{
		$hasActiveClassUnit = $student->gradebooks()
			->whereHas("classUnit.periods", function ($query) {
				$query->where("period_start", "<=", now())
					->where("period_end", ">=", now());
			})->exists();

		if (!$hasActiveClassUnit) {
			return \Response::json([
				"success" => false,
				"errors" => ["STUDENT_NOT_IN_ACTIVE_CLASS_UNIT"]
			], 422);
		}

		AccountAccess::where("student_id", $student->id)->delete();
		$accountAccess = new AccountAccess();
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
		$students = Student::whereNull("leave_date");
		if ($request->has("classUnitId")) {
			$students->whereHas("gradebooks", function ($gradebookQuery) use ($request) {
				$gradebookQuery->where("class_unit_id", "=", $request->input("classUnitId"));
			});
		}

		return $students->with("accountAccesses")->get()->map(fn($student) => [
			"firstName" => $student->person->first_name,
			"secondName" => $student->person->second_name,
			"lastName" => $student->person->last_name,
			"studentId" => $student->id,
			"accessWords" => $student->accountAccesses->filter(function ($access) {
				return $access->guardian_id == null;
			})->first()
		]);
	}

	public function generateAccessesDocument(Request $request)
	{
		$validatedData = $request->validate([
			"ids" => "required|array"
		]);

		$generator = new AccountAccessDocumentGenerator(AccessType::STUDENT, $validatedData["ids"]);
		return $generator->generateDocument();
	}

	private function checkIfRegistryIsActive(StudentRegistry $studentRegistry)
	{
		if ($studentRegistry->isArchived()) {
			throw new RegistryArchivedException("student");

		}
	}

	public function getStudentInfo(Request $request)
	{
		$student = Student::getStudentFromAccessId($request);

		return [
			"id" => $student->id,
			"firstName" => $student->person->first_name,
			"secondName" => $student->person->first_name,
			"lastName" => $student->person->last_name,
			"residenceAddress" => new ResidenceAddressResource($student->person->residenceAddress),
			"gender" => $student->person->gender
		];
	}
}
