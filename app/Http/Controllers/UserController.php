<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Utilities\AccountEventLogger;
use App\Utilities\ValidatorAssistant;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
	public function updateEmailAddress(Request $request) {
		$validator = ValidatorAssistant::validate($request, [
			"email" => "required|unique:users,email",
			"password" => "required",
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		if (!Hash::check($validated["password"], $request->user()->password)) {
			return Response::json([
				"success" => false,
				"errors" => [
					"WRONG_PASSWORD"
				]
			], 400);
		}

		$request->user()->update([
			"email" => $validated["email"]
		])->save();
		$request->user()->save();

		AccountEventLogger::log($request, AccountEventType::CREDENTIALS_CHANGED);

		return Response::json([
			"success" => true
		]);
	}

	public function updatePassword(Request $request) {
		$validator = ValidatorAssistant::validate($request, [
			"oldPassword" => "required|current_password",
			"newPassword" => "required|min:8",
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		if (!Hash::check($validated["oldPassword"], $request->user()->password)) {
			return Response::json([
				"success" => false,
				"errors" => [
					"WRONG_PASSWORD"
				]
			], 400);
		}

		Auth::logoutOtherDevices($request->input("oldPassword"));

		$request->user()->update([
			"password" => Hash::make($validated["newPassword"])
		]);
		$request->user()->save();

		AccountEventLogger::log($request, AccountEventType::CREDENTIALS_CHANGED);

		return Response::json([
			"success" => true
		]);
	}
}
