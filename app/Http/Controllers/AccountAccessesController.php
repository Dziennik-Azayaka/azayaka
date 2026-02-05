<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Enums\FrontendModule;
use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Utilities\AccountEventLogger;
use App\Utilities\ValidatorAssistant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AccountAccessesController extends Controller
{
	public function lookup(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"code" => "required|string"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$code = $validator["data"]["code"];

		$activation_code = AccountAccess::where("words", $code)->first();

		if ($activation_code) {
			session(["activation_code" => $activation_code->words]);
			session(["activation_step" => "code_found"]);
			session()->save();

			$activation_code_info = $this->getFirstAndLastNameFromActivationCode($activation_code);

			return [
				"success" => true,
				"firstName" => $activation_code_info["firstName"],
				"lastName" => $activation_code_info["lastName"],
			];
		} else {
			return Response::json([
				"success" => false,
				"errors" => [
					"CODE_NOT_FOUND"
				]
			], 404);
		}
	}

	public function checkEmailAvailability(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"email" => "required|email"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$email = $validator["data"]["email"];

		session(["activation_email" => $email]);
		if (User::whereEmail($email)->exists()) {
			session(["activation_step" => "attach_to_account"]);
			session()->save();
			return Response::json([
				"available" => false,
			]);
		} else {
			session(["activation_step" => "email_available"]);
			session()->save();
			return Response::json([
				"available" => true,
			]);
		}
	}

	public function createAccountOrAttachAccess(Request $request)
	{
		$signedIn = Auth::check();

		$validator = ValidatorAssistant::validate($request, [
			"code" => "required|string",
			"password" => $signedIn ? "nullable|exclude" : "required|min:8|max:255",
			"email" => $signedIn ? "nullable|exclude" : "required|email|max:255"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$data = $validator["data"];

		$activation_code = AccountAccess::where("words", $data["code"])->first();

		if (!$activation_code) {
			return Response::json([
				"success" => false,
				"error" => ["ACTIVATION_CODE_NOT_FOUND"],
			], 404);
		}

		$activation_code_info = $this->getFirstAndLastNameFromActivationCode($activation_code);

		if (!$signedIn && User::whereEmail($data["email"])->exists()) {
			$user = User::whereEmail($data["email"])->first();
			if (!\Hash::check($data["password"], $user->password)) {
				return Response::json([
					"success" => false,
					"error" => ["WRONG_PASSWORD"]
				], 401);
			}
		} else {
			$user = User::create([
				"email" => $data["email"],
				"password" => bcrypt($data["password"]),
				"name" => $activation_code_info["firstName"] . " " . $activation_code_info["lastName"],
			]);
			$user->save();

			event(new Registered($user));
		}

		$activation_code->user_id = $user->id;
		$activation_code->words = null;
		$activation_code->save();
		session(["activation_code" => "", "activation_step" => "not_started"]);

		Auth::login($user);
		$request->session()->regenerateToken();
		AccountEventLogger::log($request, AccountEventType::SUCCESSFUL_LOGIN_ATTEMPT);

		return \Response::json([
			"success" => true
		], 201);
	}

	public function status()
	{
		$step = session("activation_step");
		return match ($step) {
			"code_found" => [
				"step" => $step,
				"code" => session("activation_code")
			],
			"email_available", "attach_to_account" => [
				"step" => $step,
				"code" => session("activation_code"),
				"email" => session("activation_email")
			],
			default => [
				"step" => "not_started"
			]
		};
	}

	private function getFirstAndLastNameFromActivationCode(AccountAccess $activation_code)
	{
		if ($activation_code->student) {
			$first_name = $activation_code->student->first_name;
			$last_name = $activation_code->student->last_name;
		} else if ($activation_code->employee) {
			$first_name = $activation_code->employee->first_name;
			$last_name = $activation_code->employee->last_name;
		} else {
			$first_name = $activation_code->guardian->first_name;
			$last_name = $activation_code->guardian->last_name;
		}

		return [
			"firstName" => $first_name,
			"lastName" => $last_name,
			"id" => $activation_code->id,
		];
	}

	public function list(Request $request)
	{
		$accesses = AccountAccess::where("user_id", $request->user()->id)->get();
		$accessesWithPersonas = [];

		foreach ($accesses as $access) {
			if ($access->guardian) {
				foreach ($access->guardian->students as $student) {
					$accessesWithPersonas[] = [
						"id" => $access->id,
						"name" => $student->first_name . " " . $student->last_name,
						"type" => "guardian",
						"updatedAt" => $access->updated_at,
						"modulesAvailable" => $this->getAvailableModules($student)
					];
				}
			}

			if ($access->student) {
				$accessesWithPersonas[] = [
					"id" => $access->id,
					"name" => $access->student->first_name . " " . $access->student->last_name,
					"type" => "student",
					"updatedAt" => $access->updated_at,
					"modulesAvailable" => $this->getAvailableModules($access->student)
				];
			}

			if ($access->employee) {
				$accessesWithPersonas[] = [
					"id" => $access->id,
					"name" => $access->employee->first_name . " " . $access->employee->last_name,
					"type" => "employee",
					"updatedAt" => $access->updated_at,
					"modulesAvailable" => $this->getAvailableModules($access->employee)
				];
			}
		}

		return [
			"email" => $request->user()->email,
			"accesses" => $accessesWithPersonas
		];
	}

	private function getAvailableModules(Student|Employee $entity)
	{
		$modules = [];

		if ($entity instanceof Student) {
			$modules[] = FrontendModule::STUDENT;
		} else {
			if ($entity->is_admin || $entity->is_headmaster || $entity->is_secretary) {
				$modules[] = FrontendModule::SECRETARY;
			}

			if ($entity->is_admin || $entity->is_headmaster) {
				$modules[] = FrontendModule::ADMINISTRATOR;
			}

			if ($entity->is_teacher) {
				$modules[] = FrontendModule::TEACHER;
				// TODO: Check if the teacher is a form tutor before assigning the register module.
				$modules[] = FrontendModule::REGISTER;
			}
		}

		return $modules;
	}
}
