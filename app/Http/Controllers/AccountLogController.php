<?php

namespace App\Http\Controllers;

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
}
