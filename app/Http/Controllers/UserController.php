<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Utilities\AccountEventLogger;
use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	public function updateEmailAddress(Request $request) {
		$validator = Validator::make($request->all(), [
			"email" => "required|unique:users,email",
			"password" => "required",
		]);

		if ($validator->fails()) {
			return Response::json([
				"success" => false,
				"errors" => $validator->errors()
			], 400);
		}

		$validated = $validator->validated();

		if (!Hash::check($validated["password"], $request->user()->password)) {
			return Response::json([
				"success" => false,
				"errors" => [
					"WRONG_PASSWORD"
				]
			], 400);
		}

		$request->user()->fill([
			"email" => $validated["email"]
		]);
		$request->user()->save();

		AccountEventLogger::log($request, AccountEventType::CREDENTIALS_CHANGED);

		return Response::json([
			"success" => true
		]);
	}

	public function updatePassword(Request $request) {
		$validator = Validator::make($request->all(), [
			"old_password" => "required|current_password",
			"new_password" => "required|min:8",
		]);

		if ($validator->fails()) {
			return Response::json([
				"success" => false,
				"errors" => $validator->errors()
			], 400);
		}

		$validated = $validator->validated();

		if (!Hash::check($validated["old_password"], $request->user()->password)) {
			return Response::json([
				"success" => false,
				"errors" => [
					"WRONG_PASSWORD"
				]
			], 400);
		}

		Auth::logoutOtherDevices($request->input("old_password"));

		$request->user()->fill([
			"password" => Hash::make($validator->validated()["new_password"])
		]);

		AccountEventLogger::log($request, AccountEventType::CREDENTIALS_CHANGED);

		return Response::json([
			"success" => true
		]);
	}
}
