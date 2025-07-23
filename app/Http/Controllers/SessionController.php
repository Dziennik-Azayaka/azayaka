<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Utilities\AccountEventLogger;
use App\Utilities\ArrayCameliser;
use DB;
use Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Response;

class SessionController extends Controller
{
	public function authenticate(Request $request)
	{
		$credentials = $request->validate([
			"email" => ["required"],
			"password" => ["required"]
		]);

		if (Auth::attempt($credentials, true)) {
			$request->session()->regenerateToken();
			AccountEventLogger::log($request, AccountEventType::SUCCESSFUL_LOGIN_ATTEMPT);
			return [
				"success" => true
			];
		} else {
			AccountEventLogger::log($request, AccountEventType::FAILED_LOGIN_ATTEMPT);
			return Response::json([
				"success" => false,
				"errors" => [
					"INVALID_USERNAME_OR_PASSWORD"
				]
			], 401);
		}
	}

	public function logout(Request $request)
	{
		AccountEventLogger::log($request, AccountEventType::LOGOUT);
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return [
			"success" => true
		];
	}

	public function currentSessions(Request $request)
	{
		$sessions = DB::table("sessions")
			->where("user_id", "=", $request->user()->id)
			->orderBy("last_activity", "desc")
			->get(["id", "ip_address", "user_agent", "last_activity"]);

		foreach ($sessions as $session) {
			$session->last_activity = date("Y-m-d\TH:i:s.u\Z", $session->last_activity);
		}

		return ArrayCameliser::camelise([
			"currentSession" => $request->session()->getId(),
			"sessions" => $sessions
		]);
	}

	public function sessionInfo(Request $request) {
		if (Auth::check()) {
			$user = $request->user();
			return [
				"loggedIn" => true,
				"email" => $user->email,
				"name" => $user->name,
			];
		} else {
			return [
				"loggedIn" => false
			];
		}
	}

	public function removeSession(Request $request) {
		$validator = Validator::make($request->all(), [
			"id" => "required|exists:sessions,id",
			"password" => "required",
		]);

		if ($validator->fails()) {
			return Response::json([
				"success" => false,
				"errors" => $validator->errors()
			], 400);
		}

		if (!Hash::check($validator->validated()["password"], Auth::user()->password)) {
			return Response::json([
				"success" => false,
				"errors" => [
					"WRONG_PASSWORD"
				]
			], 401);
		}

		$sessionId = $validator->validated()["id"];

		if ($sessionId == $request->session()->getId()) {
			return Response::json([
				"success" => false,
				"errors" => [
					"INVALID_SESSION_ID"
				]
			], 400);
		}

		DB::table("sessions")
			->where("id", $sessionId)
			->delete();

		AccountEventLogger::log($request, AccountEventType::LOGGED_OUT_BY_ANOTHER_DEVICE);

		return [
			"success" => true
		];
	}

	public function logoutOtherDevices(Request $request) {
		try {
			Auth::logoutOtherDevices($request->input("password"));
		} catch (AuthenticationException) {
			return Response::json([
				"success" => false,
				"errors" => [
					"WRONG_PASSWORD"
				]
			], 401);
		}
		AccountEventLogger::log($request, AccountEventType::LOGGED_OUT_BY_ANOTHER_DEVICE);
		return [
			"success" => true
		];
	}
}
