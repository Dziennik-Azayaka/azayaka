<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
	public function list(Student $student) {
		return $student->guardians()->get()->toresourceCollection();
	}

	public function create(Request $request, Student $student) {
		$validated = ValidatorAssistant::validate($request, [
			"firstName" => "required|string|max:255",
			"lastName" => "required|string|max:255",
			"email" => "nullable|email|max:255",
			"phoneNumber" => "nullable|max:16"
		]);

		$guardian = new Guardian();
		$guardian->student_id = $student->id;
		$guardian->first_name = $validated["firstName"];
		$guardian->last_name = $validated["lastName"];
		$guardian->email = $validated["email"] ?? null;
		$guardian->phone_number = $validated["phoneNumber"] ?? null;
		$guardian->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(Request $request, Guardian $guardian) {
		$validated = ValidatorAssistant::validate($request, [
			"firstName" => "required|string|max:255",
			"lastName" => "required|string|max:255",
			"email" => "nullable|email|max:255",
			"phoneNumber" => "nullable|max:16"
		]);

		$guardian->first_name = $validated["firstName"];
		$guardian->last_name = $validated["lastName"];
		$guardian->email = $validated["email"] ?? null;
		$guardian->phone_number = $validated["phoneNumber"] ?? null;
		$guardian->save();

		return \Response::json([
			"success" => true
		]);
	}

	public function destroy(Guardian $guardian) {
		$guardian->delete();
		return \Response::json([
			"success" => true
		]);
	}
}
