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

			return [
				"found" => true,
				"firstName" => $activation_code->first_name,
				"lastName" => $activation_code->last_name
			];
		} else {
			return Response::json([
				"found" => false,
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

	public function createAccount(Request $request)
	{
		$validator = Validator::make($request->all(), [
			"code" => "required|string",
			"password" => "required|min:8|max:255",
			"email" => "required|email|max:255|unique:users"
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
				"error" => "Couldn't find activation code.",
			], 404);
		}

		$user = User::create([
			"email" => $data["email"],
			"password" => bcrypt($data["password"]),
			"name" => $activation_code->first_name . " " . $activation_code->last_name,
		]);
		$user->save();

		event(new Registered($user));

		Auth::login($user);
		$request->session()->regenerateToken();

		$activation_code->delete();

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
}
