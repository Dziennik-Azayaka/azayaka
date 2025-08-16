<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Utilities\AccountEventLogger;
use App\Utilities\ArrayCameliser;
use App\Utilities\ValidatorAssistant;
use DB;
use Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;

class SessionController extends Controller
{
	public function authenticate(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"email" => ["required", "email"],
			"password" => ["required"]
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$credentials = $validator["data"];

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
		return redirect("/authentication");
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
		$validator = ValidatorAssistant::validate($request, [
			"id" => "required|exists:sessions,id",
			"password" => "required|current_password"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$sessionId = $validator["data"]["id"];

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

		$request->user()->remember_token = null;
		$request->user()->save();

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
