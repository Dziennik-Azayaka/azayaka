<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Utilities\AccountEventLogger;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Response;

class UserController extends Controller
{
	public function updateEmailAddress(Request $request) {
		$validated = ValidatorAssistant::validate($request, [
			"email" => "required|unique:users,email",
			"password" => "required",
		]);

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
		]);
		$request->user()->save();

		AccountEventLogger::log($request, AccountEventType::CREDENTIALS_CHANGED);

		return Response::json([
			"success" => true
		]);
	}

	public function updatePassword(Request $request) {
		$validated = ValidatorAssistant::validate($request, [
			"oldPassword" => "required|current_password",
			"newPassword" => "required|min:8",
		]);

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
