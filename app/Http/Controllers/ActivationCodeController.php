<?php

namespace App\Http\Controllers;

use App\Models\ActivationCode;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Response;

class ActivationCodeController extends Controller
{
	public function lookup(Request $request)
	{
		$validator = Validator::make($request->all(), [
			"code" => "required|string"
		]);

		if ($validator->fails()) {
			return Response::json([
				"success" => false,
				"errors" => $validator->errors()
			], 400);
		}

		$code = $validator->validated()["code"];

		$activation_code = ActivationCode::where("words", $code)->first();

		if ($activation_code) {
			session(["activation_code" => $activation_code->words]);
			session(["activation_step" => "code_found"]);
			session()->save();

			$activation_code_info = $this->getFirstAndLastNameFromActivationCode($activation_code);

			return [
				"success" => true,
				"firstName" => $activation_code_info["firstName"],
				"lastName" => $activation_code_info["lastName"],
				"accessType" => $activation_code_info["accessType"],
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
		$validator = Validator::make($request->all(), [
			"email" => "required|email"
		]);

		if ($validator->fails()) {
			return Response::json([
				"success" => false,
				"errors" => $validator->errors()
			], 400);
		}

		$email = $validator->validated()["email"];

		if (User::whereEmail($email)->exists()) {
			return Response::json([
				"available" => false,
			]);
		} else {
			session(["activation_email" => $email]);
			session(["activation_step" => "email_available"]);
			session()->save();
			return Response::json([
				"available" => true,
			]);
		}
	}

	public function createAccountOrAttachAccess(Request $request)
	{
		$validator = Validator::make($request->all(), [
			"code" => "required|string",
			"password" => "required|min:8|max:255",
			"email" => "required|email|max:255"
		]);

		if ($validator->fails()) {
			return Response::json([
				"success" => false,
				"errors" => $validator->errors()
			], 400);
		}

		$data = $validator->validated();

		$activation_code = ActivationCode::where("words", $data["code"])->first();

		if (!$activation_code) {
			return Response::json([
				"success" => false,
				"error" => ["ACTIVATION_CODE_NOT_FOUND"],
			], 404);
		}

		$activation_code_info = $this->getFirstAndLastNameFromActivationCode($activation_code);

		if (User::whereEmail($data["email"])->exists()) {
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

		if ($activation_code_info["accessType"] == "teacher") {
			\DB::table("users_teachers")->insert([
				"user_id" => $user->id,
				"teacher_id" => $activation_code->id
			]);
		} else {
			\DB::table("users_students")->insert([
				"user_id" => $user->id,
				"student_id" => $activation_code->id,
				"acts_as" => $activation_code->acts_as,
			]);
		}

		$activation_code->delete();

		Auth::login($user);
		$request->session()->regenerateToken();

		return [
			"success" => true,
		];
	}

	public function status()
	{
		$step = session("activation_step");
		return match ($step) {
			"codeFound" => [
				"step" => $step,
				"code" => session("activation_code")
			],
			"emailAvailable" => [
				"step" => $step,
				"code" => session("activation_code"),
				"email" => session("activation_email")
			],
			default => [
				"step" => "not_started"
			]
		};
	}

	private function getFirstAndLastNameFromActivationCode(ActivationCode $activation_code)
	{
		$first_name = "";
		$last_name = "";
		$access_type = "student";

		if ($activation_code->student) {
			$first_name = $activation_code->student->first_name;
			$last_name = $activation_code->student->last_name;
		} else {
			$first_name = $activation_code->teacher->first_name;
			$last_name = $activation_code->teacher->last_name;
			$access_type = "teacher";
		}

		return [
			"firstName" => $first_name,
			"lastName" => $last_name,
			"accessType" => $access_type,
			"id" => $activation_code->id,
		];
	}
}
