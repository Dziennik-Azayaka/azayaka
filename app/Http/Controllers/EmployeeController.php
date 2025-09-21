<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;
use Response;

class EmployeeController extends Controller
{
	public function list()
	{
		$employees = Employee::all()->toResourceCollection();
		return $employees;
	}

	public function create(Request $request)
	{
		$validator = $this->validateEmployeeData($request);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		$shortcut = $data["shortcut"] ?? $this->generateShortcut($data["firstName"], $data["lastName"]);
		if ($shortcut == null) {
			return Response::json([
				"success" => false,
				"errors" => [
					"FAILURE_GENERATING_SHORTCUT"
				]
			]);
		}

		$employee = new Employee();
		$employee->first_name = $data["firstName"];
		$employee->last_name = $data["lastName"];
		$employee->shortcut = $shortcut;
		$employee->is_admin = $data["isAdmin"];
		$employee->is_headmaster = $data["isHeadmaster"];
		$employee->is_secretary = $data["isSecretary"];
		$employee->is_teacher = $data["isTeacher"];
		$employee->active = true;
		$employee->save();

		return [
			"success" => true
		];
	}

	public function update(Employee $employee, Request $request)
	{
		$validator = $this->validateEmployeeData($request);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		$shortcut = $data["shortcut"] ?? $this->generateShortcut($data["firstName"], $data["lastName"]);
		if ($shortcut == null) {
			return Response::json([
				"success" => false,
				"errors" => [
					"FAILURE_GENERATING_SHORTCUT"
				]
			]);
		}

		$employee->first_name = $data["firstName"];
		$employee->last_name = $data["lastName"];
		$employee->shortcut = $shortcut;
		$employee->is_admin = $data["isAdmin"];
		$employee->is_headmaster = $data["isHeadmaster"];
		$employee->is_secretary = $data["isSecretary"];
		$employee->is_teacher = $data["isTeacher"];
		$employee->active = true;
		$employee->save();

		return [
			"success" => true
		];
	}

	private function generateShortcut($firstName, $lastName): string|null
	{
		$firstName = strtoupper($firstName);
		$lastName = strtoupper($lastName);

		$firstNamePart = substr($firstName, 0, 1);

		// Check the most likely shortcuts: first letter of the first name, and the first letter of the second name
		// If that doesn't work, check 1F + 2L.
		$firstShortcut = $firstNamePart . substr($lastName, 0, 1);
		if (!Employee::where("shortcut", $firstShortcut)->exists()) {
			return $firstShortcut;
		}

		if (strlen($lastName) > 1) {
			$secondShortcut = $firstNamePart . substr($lastName, 1, 1);
			if (!Employee::where("shortcut", $secondShortcut)->exists()) {
				return $secondShortcut;
			}
		}

		// If these are taken, try alternate shortcuts, which takes more compute resources.
		$possibleShortcuts = [];

		// Generate combinations of the first letter of the first name
		// with different single letters from the last name (e.g., J + F, J + G, etc.)
		for ($i = 2; $i < 3; $i++) {
			$possibleShortcuts[] = $firstNamePart . substr($lastName, $i, 1);
		}

		// Generate F + L combos (e.g., J + N, J + NO, J + NOW)
		// speeds up operations when there are a lot of employees with the same name
		for ($i = 2; $i <= min(3, strlen($lastName)); $i++) {
			$possibleShortcuts[] = $firstNamePart . substr($lastName, 0, $i);
		}

		$existingShortcuts = Employee::whereIn("shortcut", $possibleShortcuts)
			->pluck("shortcut")
			->toArray();

		foreach ($possibleShortcuts as $shortcut) {
			if (!in_array($shortcut, $existingShortcuts)) {
				return $shortcut;
			}
		}

		return null;
	}

	private function validateEmployeeData(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"firstName" => ["required", "max:255"],
			"lastName" => ["required", "max:255"],
			"shortcut" => ["nullable", "max:4", "unique:employees"],
			"isAdmin" => ["required", "boolean"],
			"isHeadmaster" => ["required", "boolean"],
			"isSecretary" => ["required", "boolean"],
			"isTeacher" => ["required", "boolean"],
		]);

		if (!$validator["data"]["isAdmin"] && !$validator["data"]["isSecretary"] &&
			!$validator["data"]["isTeacher"] && !$validator["data"]["isHeadmaster"]) {
			return [
				"success" => false,
				"errors" => [
					"EMPLOYEE_MUST_HAVE_AT_LEAST_ONE_ROLE_ASSIGNED"
				]
			];
		}

		return $validator;
	}
}
