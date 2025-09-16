<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
	public function list() {
		$employees = Employee::all()git add . ->toResourceCollection();
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
		$employee->shortcut = $data["shortcut"] ?? substr($data["firstName"], 0, 1) . substr($data["lastName"], 0, 1);
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

	public function update(Employee $employee, Request $request) {
		$validator = $this->validateEmployeeData($request);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}
		$data = $validator["data"];

		$employee->first_name = $data["firstName"];
		$employee->last_name = $data["lastName"];
		$employee->shortcut = $data["shortcut"] ?? substr($data["firstName"], 0, 1) . substr($data["lastName"], 0, 1);
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

	private function validateEmployeeData(Request $request) {
		return ValidatorAssistant::validate($request, [
			"firstName" => ["required", "max:255"],
			"lastName" => ["required", "max:255"],
			"shortcut" => ["nullable", "max:255"],
			"isAdmin" => ["required", "boolean"],
			"isHeadmaster" => ["required", "boolean"],
			"isSecretary" => ["required", "boolean"],
			"isTeacher" => ["required", "boolean"],
		]);
	}
}
