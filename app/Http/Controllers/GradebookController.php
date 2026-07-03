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
		$studentIdsSize = count($validated["studentIds"]);

		$existingEntries = GradebookStudents::where("gradebook_id", $gradebook->id)->get();
		$pivotEntries = [];
		$now = now();
		for ($i = 0; $i < $studentIdsSize; $i++) {
			if ($existingEntries->contains("student_id", $validated["studentIds"][$i])) {
				$existingEntry = $existingEntries->firstWhere("student_id", $validated["studentIds"][$i]);
				$pivotEntries[] = [
					"student_id" => $validated["studentIds"][$i],
					"gradebook_id" => $gradebook->id,
					"position" => $validated["positions"][$i],
					"date_from" => $existingEntry->date_from,
					"date_to" => $existingEntry->date_to,
					"created_at" => $existingEntry->created_at,
					"updated_at" => $now
				];
				continue;
			}
			$pivotEntries[] = [
				"student_id" => $validated["studentIds"][$i],
				"gradebook_id" => $gradebook->id,
				"position" => $validated["positions"][$i],
				"date_from" => $gradebook->startingClassificationPeriod->period_start,
				"date_to" => null,
				"created_at" => $now,
				"updated_at" => $now
			];
		}

		\DB::transaction(function () use ($pivotEntries, $gradebook) {
			GradebookStudents::where("gradebook_id", $gradebook->id)->delete();
			GradebookStudents::insert($pivotEntries);
		});

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
