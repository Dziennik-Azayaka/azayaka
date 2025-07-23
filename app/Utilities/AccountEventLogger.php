<?php

namespace App\Utilities;

use App\Enums\AccountEventType;
use App\Models\AccountLog;
use App\Models\User;
use Illuminate\Http\Request;

class AccountEventLogger
{
	static function log(Request $request, AccountEventType $eventType): void
	{
		$log = new AccountLog;

		if ($request->user()) {
			$log->user_id = $request->user()->id;
		} else if (User::where("email", $request->input("email"))->exists()) {
			$log->user_id = User::where("email", $request->input("email"))->first()->id;
		} else {
			return;
		}

		$log->event_type = $eventType->value;
		$log->ip = $request->ip();
		$log->user_agent = $request->userAgent();
		$log->save();
	}
}
