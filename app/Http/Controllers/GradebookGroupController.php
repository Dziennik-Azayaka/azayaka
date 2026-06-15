<?php

namespace App\Http\Controllers;

use App\Enums\GradebookSubjectType;
use App\Models\Employee;
use App\Models\Gradebook;
use App\Models\GradebookGroup;
use App\Models\GradebookGroupSubject;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradebookGroupController extends Controller
{
	// TODO: Figure out assigning subjects to the entire class.
	// Right now the workaround will be to create a group with everybody in it.
	function list(Gradebook $gradebook)
	{
		return $gradebook
			->groups()->with(["groupSubjects.subject", "groupSubjects.teachers", "students"])
			->get()->toResourceCollection();
	}

	public function create(Request $request, Gradebook $gradebook)
	{
		$validated = $request->validate([
			"name" => ["required", "string", "max:255"],
			"shortcut" => ["required", "string", "max:8"],
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
		$validated = $request->validate([
			"name" => ["required", "string", "max:255"],
			"shortcut" => ["required", "string", "max:8"],
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
		$gradebookGroup->delete();
		return [
			"success" => true
		];
	}

	public function addSubject(Request $request, GradebookGroup $gradebookGroup)
	{
		$validated = $request->validate([
			"subject_id" => ["required", "exists:subjects,id"],
			"description" => ["required", "string", "max:255", Rule::enum(GradebookSubjectType::class)],
			"teachers" => ["nullable", "array"],
			"teachers.*" => ["exists:employees,id"]
		]);

		if ($gradebookGroup->groupSubjects()->where("subject_id", $validated["subject_id"])->exists()) {
			return \Response::json([
				"success" => false,
				"errors" => [
					"SUBJECT_ALREADY_ASSIGNED_TO_GROUP"
				]
			], 409);
		}

		$groupSubject = $gradebookGroup->groupSubjects()->create([
			"subject_id" => $validated["subject_id"],
			"description" => $validated["description"],
		]);

		if (!empty($validated["teachers"])) {
			$groupSubject->teachers()->sync($validated["teachers"]);
		}

		return [
			"success" => true
		];
	}

	public function updateSubject(Request $request, string $gradebookGroup, GradebookGroupSubject $groupSubject)
	{
		$validated = $request->validate([
			"description" => ["required", "string", "max:255"],
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
		$groupSubject->delete(); // Since there's onDelete("cascade") on all foreignIds, any teachers will be deleted too.
		return [
			"success" => true,
		];
	}

	public function updateTeachers(Request $request, string $gradebookGroup, GradebookGroupSubject $groupSubject)
	{
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
