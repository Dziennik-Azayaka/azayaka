<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
	public function list(Request $request)
	{
		$preferences = UserPreference::where("user_id", $request->user()->id)->get();

		return $preferences->mapWithKeys(fn(UserPreference $preference) => [
			$preference->key => $preference->value
		]);
	}

	public function update(Request $request, string $key)
	{
		$validated = $request->validate([
			"value" => "required|string|max:65535"
		]);

		UserPreference::updateOrCreate(
			["user_id" => $request->user()->id, "key" => $key],
			["value" => $validated["value"]]
		);

		return ["success" => true];
	}
}
