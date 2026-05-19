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
	public function list(StudentRegistry $studentRegistry)
	{
		return $studentRegistry->students()->with(["person", "person.residenceAddress"])->get()->toResourceCollection();
	}

	public function show(Student $student)
	{
		return $student->load(["person", "person.residenceAddress"])->toResource();
	}

	public function create(Request $request, StudentRegistry $studentRegistry)
	{
		$validated = $request->validate([
			"personId" => ["required", "exists:people,id"],
			"admission_date" => ["required", "date"]
		]);

		$student = new Student();
			$student->person_id = $validated["personId"];
			$student->student_registry_id = $studentRegistry->id;
			$student->admission_date = $validated["admission_date"];
			try {
			$student->saveOrFail();
		} catch (\Throwable $e) {
			\Log::error($e);
			return response()->json([
				"success" => false,
				"errors" => ["UNKNOWN_SERVER_ERROR"]
			], 500);
		}

		return \Response::json([
			"success" => true,
			"studentId" => $student->id,
		], 201);
	}

	public function update(Request $request, Student $student)
	{
		$this->checkIfRegistryIsActive($student->studentRegistry);

		$validated = $request->validate([
			"admissionDate" => ["required", "date"],
			"leave_date" => ["nullable", "date"],
			"leave_reason" => ["nullable", "string", "max:255"],
		]);

		$student->admission_date = $validated["admissionDate"];
		$student->leave_date = $validated["leave_date"] ?? null;
		$student->leave_reason = $validated["leave_reason"] ?? null;

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
