<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function list() {
		return Subject::all(["id", "name", "shortcut", "active"]);
	}

	public function create(Request $request) {
		$validator = ValidatorAssistant::validate($request, [
			"name" => ["string", "max:255", "min:3", "required"],
			"shortcut" => ["string", "max:4", "unique:subjects", "required"]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$subject = new Subject();
		$subject->name = $validated["name"];
		$subject->shortcut = $validated["shortcut"];
		$subject->save();

		return [
			"success" => true
		];
	}

	public function update(Subject $subject, Request $request) {
		$validator = ValidatorAssistant::validate($request, [
			"name" => ["string", "max:255", "min:3"],
			"shortcut" => ["string", "max:4", Rule::unique("subjects")->ignore($subject->id)]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$subject->update($validator["data"]);

		return [
			"success" => true
		];
	}

	public function archive(Subject $subject) {
		$subject->active = !$subject->active;
		$subject->save();
		return [
			"success" => true
		];
	}
}
