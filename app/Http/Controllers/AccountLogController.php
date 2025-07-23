<?php

namespace App\Http\Controllers;

use App\Enums\AccountEventType;
use App\Models\AccountLog;
use App\Utilities\ArrayCameliser;
use Illuminate\Http\Request;

class AccountLogController extends Controller
{
	public function list(Request $request)
	{
		$paginator = AccountLog::where("user_id", $request->user()->id)->paginate(50, [
			"event_type",
			"ip",
			"user_agent",
			"created_at"
		]);

		return ArrayCameliser::camelise($paginator->toArray());
	}

	public function getDateOfLastUpdateToCredentials(Request $request)
	{
		return [
			"success" => true,
			"date" => AccountLog::where("user_id", $request->user()->id)
				->where("event_type", AccountEventType::CREDENTIALS_CHANGED->value)
				->orderBy("created_at", "DESC")
				->first()
				?->created_at
		];
	}
}
