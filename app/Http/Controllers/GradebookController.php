<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachStudentsToGradebookRequest;
use App\Http\Requests\CreateGradebookRequest;
use App\Http\Resources\GradebookStudentResource;
use App\Http\Resources\StudentGradebookResource;
use App\Models\ClassUnit;
use App\Models\Gradebook;
use App\Models\GradebookStudents;
use App\Models\Student;
use App\Utilities\CaseConverter;
use Illuminate\Http\Request;

class GradebookController extends Controller
{
	public function list(Request $request, int $schoolUnitId)
	{
		return Gradebook::inSchoolUnit($schoolUnitId)
			->when($request->has("classUnitId"), fn($query) => $query->forClassUnit($request->input("classUnitId")))
			->when($request->has("schoolYear"), fn($query) => $query->inSchoolYear($request->input("schoolYear")))
			->with(["classUnit", "startingClassificationPeriod"])
			->get()
			->toResourceCollection();
	}

	public function create(CreateGradebookRequest $request)
	{
		$validated = $request->validated();

		$classUnit = ClassUnit::findOrFail($validated["classUnitId"]);
		$this->authorize("create", [Gradebook::class, $classUnit]);

		$gradebook = Gradebook::create(CaseConverter::toSnakeCase($validated));
		return \Response::json([
			"success" => true,
			"gradebookId" => $gradebook->id
		], 201);
	}

	public function listStudents(Request $request, Gradebook $gradebook)
	{
		$pivotEntries = GradebookStudents::where("gradebook_id", $gradebook->id)
			->with(["student", "student.person"])->get();
		return GradebookStudentResource::collection($pivotEntries);
	}

	public function attachStudentsToGradebook(AttachStudentsToGradebookRequest $request, Gradebook $gradebook)
	{
		$this->authorize("manageStudents", [$gradebook]);

		$validated = $request->validated();

		$existingEntries = GradebookStudents::where("gradebook_id", $gradebook->id)
			->get()
			->keyBy("student_id");

		$syncData = [];

		foreach ($validated["studentIds"] as $index => $studentId) {
			$existingEntry = $existingEntries->get($studentId);

			$syncData[$studentId] = [
				"position" => $validated["positions"][$index],
				"date_from" => $existingEntry?->date_from
					?? $gradebook->startingClassificationPeriod->period_start,
				"date_to" => $existingEntry?->date_to,
			];
		}

		$gradebook->students()->sync($syncData);

		return \Response::json([
			"success" => true
		]);
	}

	public function getStudentGradebooks(Request $request)
	{
		$student = Student::getStudentFromAccessId($request);
		$gradebooks = GradebookStudents::where("student_id", $student->id)
			->with(["gradebook", "gradebook.classUnit"])->get();

		return StudentGradebookResource::collection($gradebooks);
	}
}
