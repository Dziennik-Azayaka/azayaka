<?php

use App\Http\Controllers\AccountAccessesController;
use App\Http\Controllers\AccountLogController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::middleware(["auth.deny", "throttle:16,1"])->group(function () {
	Route::post("/api/login", [SessionController::class, "authenticate"]);
});

Route::middleware(["throttle:16,1"])->group(function () {
	Route::get("/api/activation/status", [AccountAccessesController::class, "status"]);
	Route::post("/api/activation/lookup", [AccountAccessesController::class, "lookup"]);
	Route::post("/api/activation/emailAvailability", [AccountAccessesController::class, "checkEmailAvailability"]);
	Route::post("/api/activation", [AccountAccessesController::class, "createAccountOrAttachAccess"]);
});

Route::get("/api/session", [SessionController::class, "sessionInfo"]);

Route::middleware(["auth", "auth.session"])->group(function () {
	Route::get("/api/sessions", [SessionController::class, "currentSessions"]);
	Route::delete("/api/sessions/remove", [SessionController::class, "removeSession"]);
	Route::delete("/api/sessions/removeAll", [SessionController::class, "logoutOtherDevices"]);
	Route::get("/api/logout", [SessionController::class, "logout"]);

	Route::put("/api/user/email", [UserController::class, "updateEmailAddress"]);
	Route::put("/api/user/password", [UserController::class, "updatePassword"]);

	Route::get("/api/user/logs", [AccountLogController::class, "list"]);
	Route::get("/api/user/logs/lastCredentialUpdate", [AccountLogController::class, "getDateOfLastUpdateToCredentials"]);

	Route::get("/api/user", [AccountAccessesController::class, "list"]);
});

// Email Verification
Route::get("/api/email/verify/{id}/{hash}", function (EmailVerificationRequest $request) {
	$request->fulfill();
	return redirect("/");
})->middleware(["auth", "signed"])->name("verification.verify");

Route::post("/api/email/verification-notification", function (Request $request) {
	$request->user()->sendEmailVerificationNotification();
	return back()->with("message", "Verification link sent!");
})->middleware(["auth", "throttle:6,1"])->name("verification.send");

// SPA
Route::view("/authentication{any?}", "authentication")->where("any", ".*")->name("login");

Route::middleware(["auth", "auth.session"])->group(function () {
	Route::view("/myaccount{any?}", "myaccount")->where("any", ".*");
});

Route::redirect("/", "/myaccount");
