<?php

namespace App\Http\Controllers;

use App\Enums\GradebookSubjectType;
use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\GradebookGroup;
use App\Models\GradebookGroupSubject;
use App\Models\Subject;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradebookGroupController extends Controller
{
	function list(Gradebook $gradebook)
	{
		$this->authorize("manageGroups", [$gradebook]);

		$gradebookSubjects = $gradebook
			->subjects()->with(["subject", "teachers"])
			->get();

		$groups = $gradebook
			->groups()->with(["groupSubjects.subject", "groupSubjects.teachers", "students"])
			->get()->toResourceCollection();

		if ($gradebookSubjects->isNotEmpty()) {
			$all = [
				"id" => null,
				"name" => "All",
				"shortcut" => "ALL",
				"isGradebookLevel" => true,
				"groupSubjects" => $gradebookSubjects->toResourceCollection(),
				"students" => [],
			];
			return [$all, ...$groups];
		}

		return $groups;
	}

	public function create(Request $request, Gradebook $gradebook)
	{
		$this->authorize("manageGroups", [$gradebook]);

		$validated = $request->validate([
			"name" => [
				"required",
				"string",
				"max:255",
				Rule::unique("gradebook_groups")
					->where(fn(Builder $query) => $query->where("gradebook_id", $gradebook->id))
			],
			"shortcut" => [
				"required",
				"string",
				"max:8",
				Rule::unique("gradebook_groups")
					->where(fn(Builder $query) => $query->where("gradebook_id", $gradebook->id))
			],
			"studentIds" => ["nullable", "array"],
			"studentIds.*" => ["exists:students,id"],
		]);

		$group = $gradebook->groups()->create([
			"name" => $validated["name"],
			"shortcut" => $validated["shortcut"],
		]);

		if (!empty($validated["studentIds"])) {
			$group->students()->sync($validated["studentIds"]);
		}

		return [
			"success" => true
		];
	}

	public function update(Request $request, GradebookGroup $gradebookGroup)
	{
		$this->authorize("manageGroups", [$gradebookGroup->gradebook]);

		$validated = $request->validate([
			"name" => [
				"required",
				"string",
				"max:255",
				Rule::unique("gradebook_groups")
					->ignore($gradebookGroup->id)
					->where(fn(Builder $query) => $query->where("gradebook_id", $gradebookGroup->gradebook->id))
			],
			"shortcut" => [
				"required",
				"string",
				"max:8",
				Rule::unique("gradebook_groups")
					->ignore($gradebookGroup->id)
					->where(fn(Builder $query) => $query->where("gradebook_id", $gradebookGroup->gradebook->id))
			],
			"studentIds" => ["nullable", "array"],
			"studentIds.*" => ["exists:students,id"],
		]);

		$gradebookGroup->update([
			"name" => $validated["name"],
			"shortcut" => $validated["shortcut"],
		]);

		$gradebookGroup->students()->sync($validated["studentIds"] ?? []);

		return [
			"success" => true
		];
	}

	public function destroy(GradebookGroup $gradebookGroup)
	{
		$this->authorize("manageGroups", [$gradebookGroup->gradebook]);

		$gradebookGroup->delete();
		return [
			"success" => true
		];
	}

	public function addSubject(Request $request, GradebookGroup $gradebookGroup)
	{
		$this->authorize("manageGroups", [$gradebookGroup->gradebook]);

		return $this->createSubject(
			$request,
			$gradebookGroup->groupSubjects(),
			$gradebookGroup->gradebook_id,
		);
	}

	public function addGradebookSubject(Request $request, Gradebook $gradebook)
	{
		$this->authorize("manageGroups", [$gradebook]);

		return $this->createSubject(
			$request,
			$gradebook->subjects(),
		);
	}

	private function createSubject(Request $request, $relationship, ?int $gradebookId = null)
	{
		$validated = $request->validate([
			"subject_id" => ["required", "exists:subjects,id"],
			"description" => ["required", "string", "max:255", Rule::enum(GradebookSubjectType::class)],
			"teachers" => ["nullable", "array"],
			"teachers.*" => ["exists:employees,id"]
		]);

		if ($relationship->where("subject_id", $validated["subject_id"])->exists()) {
			return \Response::json([
				"success" => false,
				"errors" => ["SUBJECT_ALREADY_ASSIGNED_TO_GROUP"]
			], 409);
		}

		$data = [
			"subject_id" => $validated["subject_id"],
			"description" => $validated["description"],
		];

		if ($gradebookId !== null) {
			$data["gradebook_id"] = $gradebookId;
		}

		$subject = $relationship->create($data);

		if (!empty($validated["teachers"])) {
			$subject->teachers()->sync($validated["teachers"]);
		}

		return ["success" => true];
	}

	public function updateSubject(Request $request, string $gradebookGroup, GradebookGroupSubject $groupSubject)
	{
		$this->authorize("manageGroups", [$groupSubject->gradebook]);

		$validated = $request->validate([
			"description" => ["required", "string", "max:255", Rule::enum(GradebookSubjectType::class)],
		]);

		$groupSubject->update([
			"description" => $validated["description"]
		]);

		return [
			"success" => true
		];
	}

	public function destroySubject(string $gradebookGroup, GradebookGroupSubject $groupSubject)
	{
		$this->authorize("manageGroups", [$groupSubject->gradebook]);

		$groupSubject->delete(); // Since there's onDelete("cascade") on all foreignIds, any teachers will be deleted too.
		return [
			"success" => true,
		];
	}

	public function updateTeachers(Request $request, string $gradebookGroup, GradebookGroupSubject $groupSubject)
	{
		$this->authorize("manageGroups", [$groupSubject->gradebook]);

		$validated = $request->validate([
			"teachers" => ["required", "array"],
			"teachers.*" => ["exists:employees,id"],
		]);
		$groupSubject->teachers()->sync($validated["teachers"]);
		return [
			"success" => true,
		];
	}
}
