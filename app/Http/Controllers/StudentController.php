<?php

namespace App\Http\Controllers;

use App\Documents\AccountAccessesActivation\AccountAccessesActivationDocument;
use App\Enums\AccessType;
use App\Exceptions\CustomValidationException;
use App\Http\Resources\ResidenceAddressResource;
use App\Models\AccountAccess;
use App\Models\ChildrenRegistry;
use App\Models\Employee;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Rules\Pesel;
use App\Utilities\AccountAccessWordsGenerator;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use App\Utilities\ValidatorAssistant\ValidatorAssistantException;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
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

		return $query->get()->toResourceCollection();
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

	public function generateOrRegenerateAccess(Student $student)
	{
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
		$students = Student::where("active", "=", "1");
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

	// TODO: Abstract this away, as it's used in EmployeeController and GuardianController as well.
	// TODO: Write tests
	public function generateAccessesDocument(Request $request)
	{
		$validatedData = $request->validate([
			"ids" => "required|array"
		]);

		$ids = array_unique($validatedData["ids"]);
		$students = Student::whereIn("id", $ids)->get();
		$studentIds = $students->pluck("id");
		$accesses = AccountAccess::whereIn("student_id", $studentIds)->get();

		$document = new AccountAccessesActivationDocument();

		foreach ($students as $student) {
			$access = $accesses->where("student_id", $student->id)->first();
			if ($access?->words == null) {
				return Response::json([
					"success" => false,
					"errors" => [
						"STUDENT_HAS_NO_ACCESS_WORDS"
					]
				], 422);
			}
			$document->addAccess(AccessType::STUDENT,
				$student->person->first_name . " " . $student->person->last_name,
				explode(",", $access->words));
		}

		$document->generateDocument();
		return $document->streamDocument();
	}

	private function checkIfRegistryIsActive(StudentRegistry $studentRegistry)
	{
		if ($studentRegistry->isArchived()) {
			throw CustomValidationException::withMessages(["STUDENT_REGISTRY_ARCHIVED"]);
		}
	}

	public function getStudentInfo(Request $request)
	{
		$accessID = $request->header("Access-ID") ?? $request->route("accessId");
		$student = AccountAccess::where("user_id", $request->user()->id)
			->where("id", $accessID)->first()->student()->with(["person", "person.residenceAddress"])->first();

		// TODO: Turn into resource?
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
