<?php

namespace App\Http\Controllers;

use App\Models\AccountLog;
use Illuminate\Http\Request;

class AccountLogController extends Controller
{
    public function list(Request $request) {
		return AccountLog::where("user_id", $request->user()->id)->get([
			"event_type",
			"ip",
			"user_agent",
			"created_at"
		]);
	}
}
