<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardianRequest;
use App\Models\Guardian;
use App\Models\Student;
use App\Utilities\CaseConverter;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
	public function list(Student $student) {
		return $student->guardians()->get()->toresourceCollection();
	}

	public function create(GuardianRequest $request, Student $student) {
		$validated = $request->validated();
		$guardian = new Guardian(CaseConverter::toSnakeCase($validated));
		$guardian->student()->associate($student);
		$guardian->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function update(GuardianRequest $request, Guardian $guardian) {
		$guardian->update(CaseConverter::toSnakeCase($request->validated()));
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
