<?php

use App\Http\Controllers\ActivationCodeController;
use App\Http\Controllers\SessionController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(["auth.deny", "throttle:16,1"])->group(function () {
	Route::post("login", [SessionController::class, "authenticate"]);
	Route::get("/api/activation/status", [ActivationCodeController::class, "status"]);
	Route::post("/api/activation/lookup", [ActivationCodeController::class, "lookup"]);
	Route::post("/api/activation/emailAvailability", [ActivationCodeController::class, "checkEmailAvailability"]);
	Route::post("/api/activation", [ActivationCodeController::class, "createAccount"]);
});

Route::get("/api/session", [SessionController::class, "sessionInfo"]);

Route::middleware(["auth"])->group(function () {
    Route::get("/api/sessions", [SessionController::class, "currentSessions"]);
});

// Email Verification
Route::get("/email/verify/{id}/{hash}", function (EmailVerificationRequest $request) {
	$request->fulfill();
	return redirect("/");
})->middleware(["auth", "signed"])->name("verification.verify");

Route::get("/email/verify", function () {
	return view("auth.verify-email");
})->middleware("auth")->name("verification.notice");

Route::post("/email/verification-notification", function (Request $request) {
	$request->user()->sendEmailVerificationNotification();
	return back()->with("message", "Verification link sent!");
})->middleware(["auth", "throttle:6,1"])->name("verification.send");

// Simple HTML views for testing
Route::get("/lite/login", function () {
	return view("login");
})->name("login");

Route::get("/lite/activation", function () {
	return view("activation");
});
