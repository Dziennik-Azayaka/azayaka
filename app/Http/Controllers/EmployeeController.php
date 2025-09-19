<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;

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

		$employee = new Employee();
		$employee->first_name = $data["firstName"];
		$employee->last_name = $data["lastName"];
		$employee->shortcut = $data["shortcut"] ?? $this->generateShortcut($data["firstName"], $data["lastName"]);
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

		$employee->first_name = $data["firstName"];
		$employee->last_name = $data["lastName"];
		$employee->shortcut = $data["shortcut"] ?? $this->generateShortcut($data["firstName"], $data["lastName"]);
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

	private function generateShortcut($firstName, $lastName)
	{
		$firstNamePart = substr($firstName, 0, 1);
		$lastNamePart = substr($lastName, 0, 3);
		$possibleShortcuts = [];

		// Generate F + L combos (e.g., J + N, J + NO, J + NOW)
		// speeds up operations when there are a lot of employees with the same name
		for ($i = 1; $i <= min(3, strlen($lastNamePart)); $i++) {
			$possibleShortcuts[] = $firstNamePart . substr($lastNamePart, 0, $i);
		}

		$existingShortcuts = Employee::whereIn('shortcut', $possibleShortcuts)
			->pluck('shortcut')
			->toArray();

		foreach ($possibleShortcuts as $shortcut) {
			if (!in_array($shortcut, $existingShortcuts)) {
				return $shortcut;
			}
		}
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

		if ($validator["data"]["isAdmin"] != true && $validator["data"]["isSecretary"] != true &&
			$validator["data"]["isTeacher"] != true && $validator["data"]["isHeadmaster"] != true) {
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
