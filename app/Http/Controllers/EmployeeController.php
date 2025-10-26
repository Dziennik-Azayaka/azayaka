<?php

namespace App\Http\Controllers;

use App\Models\AccountAccess;
use App\Models\Employee;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;
use Response;

class EmployeeController extends Controller
{
	public function list()
	{
		return Employee::all()->toResourceCollection();
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

		$accountAccess = $this->generateAccess($employee);

		return [
			"success" => true,
			"words" => $accountAccess
		];
	}

	public function update(Employee $employee, Request $request)
	{
		$validator = $this->validateEmployeeData($request);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		if ($employee->first_name != $data["firstName"] || $employee->last_name != $data["lastName"]) {
			$shortcut = $data["shortcut"] ?? $this->generateShortcut($data["firstName"], $data["lastName"]);
			if ($shortcut == null) {
				return Response::json([
					"success" => false,
					"errors" => [
						"FAILURE_GENERATING_SHORTCUT"
					]
				]);
			}

			$employee->shortcut = $shortcut;
		}

		if ($employee->is_admin == true && $data["isAdmin"] == false) {
			if (Employee::where("employees.is_admin", "true")->count() < 2) {
				return Response::json([
					"success" => false,
					"errors" => [
						"AT_LEAST_ONE_ADMIN_REQUIRED"
					]
				]);
			}
		}

		$employee->first_name = $data["firstName"];
		$employee->last_name = $data["lastName"];
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

	public function archive(Employee $employee, Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"password" => "required|current_password"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$employee->active = !$employee->active;
		$employee->save();

		if (!$employee->active) {
			AccountAccess::where("employee_id", $employee->id)->delete();
		}

		return [
			"success" => true
		];
	}

	public function checkDeletionEligibility(Employee $employee, Request $request)
	{
		// TODO: Implement proper eligibility checking once classes have been figured out
		return true;
	}

	public function delete(Employee $employee, Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"password" => "required|current_password"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		AccountAccess::where("employee_id", $employee->id)->delete();

		$employee->delete();

		return [
			"success" => true
		];
	}

	public function getEmployeeAccess(Employee $employee)
	{
		return AccountAccess::where("employee_id", $employee->id)->get()->toResourceCollection();
	}

	public function revokeEmployeeAccess(Employee $employee)
	{
		AccountAccess::where("employee_id", $employee->id)->delete();
		return [
			"success" => true
		];
	}

	public function regenerateEmployeeAccess(Employee $employee)
	{
		$existingAccess = AccountAccess::where("employee_id", $employee->id)->delete();

		$accountAccess = $this->generateAccess($employee);
		return [
			"success" => true,
			"words" => $accountAccess->words
		];
	}

	private function generateAccess(Employee $employee): AccountAccess
	{
		$accountAccess = new AccountAccess();
		$accountAccess->employee_id = $employee->id;
		$accountAccess->words = "1,2,3,4,5,6,7,8,9,10"; // TODO: Replace with a dictionary of polish words.
		$accountAccess->save();
		return $accountAccess;
	}

	private function generateShortcut(string $firstName, string $lastName): string|null
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
