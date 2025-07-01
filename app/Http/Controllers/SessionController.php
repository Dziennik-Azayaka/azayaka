<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
			return redirect("/");
		} else {
			return Redirect::back()->withErrors(["message" => "Niepoprawna nazwa użytkownika bądź hasło."]); // TODO: Use translations
		}
	}

	public function logout(Request $request)
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		return redirect("/");
	}

	public function currentSessions(Request $request)
	{
		return DB::table('sessions')
			->where("user_id", "=", $request->user()->id)
			->orderBy("last_activity", "desc")
			->get(["ip_address", "user_agent", "last_activity"]);
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
}
