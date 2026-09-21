<?php

namespace App\Http\Controllers;

use App\Enums\UserPreferenceType;
use App\Exceptions\NotFoundException;
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
		$keyValidationRules = UserPreferenceType::tryFromName(ucfirst($key));
		if ($keyValidationRules == null) {
			throw new NotFoundException("USER_PREFERENCE_KEY");
		}
		$validated = $request->validate([
			"value" => "required|$keyValidationRules->value"
		]);

		UserPreference::updateOrCreate(
			["user_id" => $request->user()->id, "key" => $key],
			["value" => $validated["value"]]
		);

		return ["success" => true];
	}
}
