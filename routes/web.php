<?php

use App\Http\Controllers\AccountAccessesController;
use App\Http\Controllers\AccountLogController;
use App\Http\Controllers\ChildrenRegistryController;
use App\Http\Controllers\ClassificationPeriodController;
use App\Http\Controllers\ClassificationPeriodDefaultsController;
use App\Http\Controllers\ClassUnitController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SchoolComplexController;
use App\Http\Controllers\SchoolUnitController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentRegistryController;
use App\Http\Controllers\SubjectController;
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

	Route::middleware(["employee.role:administrator"])->group(function () {
		Route::get("/api/schoolComplex", [SchoolComplexController::class, "list"]);
		Route::post("/api/schoolComplex", [SchoolComplexController::class, "create"]);
		Route::put("/api/schoolComplex/{schoolComplex}", [SchoolComplexController::class, "update"]);
		Route::get("/api/schoolUnits", [SchoolUnitController::class, "list"]);
		Route::post("/api/schoolUnits", [SchoolUnitController::class, "create"]);
		Route::put("/api/schoolUnits/{schoolUnit}", [SchoolUnitController::class, "update"]);
		Route::put("/api/schoolUnits/{schoolUnit}/activity", [SchoolUnitController::class, "archive"]);

		Route::get("/api/employees", [EmployeeController::class, "list"]);
		Route::post("/api/employees", [EmployeeController::class, "create"]);
		Route::put("/api/employees/{employee}", [EmployeeController::class, "update"]);
		Route::put("/api/employees/{employee}/activity", [EmployeeController::class, "archive"]);
		Route::get("/api/employees/{employee}/access", [EmployeeController::class, "getEmployeeAccess"]);
		Route::post("/api/employees/{employee}/access/regenerate", [EmployeeController::class, "regenerateEmployeeAccess"]);
		Route::delete("/api/employees/{employee}/access", [EmployeeController::class, "revokeEmployeeAccess"]);
		Route::get("/api/employees/accesses", [EmployeeController::class, "listEmployeeAccesses"]);
		Route::patch("/api/employees/accesses", [EmployeeController::class, "massUpdateAccess"]);

		Route::get("/api/subjects", [SubjectController::class, "list"]);
		Route::post("/api/subjects", [SubjectController::class, "create"]);
		Route::put("/api/subjects/{subject}", [SubjectController::class, "update"]);
		Route::put("/api/subjects/{subject}/activity", [SubjectController::class, "archive"]);

		Route::get("/api/schoolUnits/{schoolUnit}/classUnits", [ClassUnitController::class, "list"]);
		Route::post("/api/schoolUnits/{schoolUnit}/classUnits", [ClassUnitController::class, "create"]);
		Route::put("/api/schoolUnits/{schoolUnit}/classUnits/{classUnit}", [ClassUnitController::class, "update"]);
		Route::delete("/api/schoolUnits/{schoolUnit}/classUnits/{classUnit}", [ClassUnitController::class, "delete"]);

		Route::get("/api/schoolUnits/{schoolUnitId}/classificationPeriods/{schoolYear}", [ClassificationPeriodController::class, "list"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/classificationPeriods/{schoolYear}", [ClassificationPeriodController::class, "save"]);
		Route::delete("/api/schoolUnits/{schoolUnitId}/classificationPeriods/{schoolYear}", [ClassificationPeriodController::class, "delete"]);
	});

	Route::middleware(["employee.role:secretary"])->group(function () {
		Route::get("/api/studentRegistries", [StudentRegistryController::class, "list"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/studentRegistry", [StudentRegistryController::class, "create"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/studentRegistry/{studentRegistry}", [StudentController::class, "create"]);
		Route::get("/api/childrenRegistries", [ChildrenRegistryController::class, "list"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/childrenRegistry", [ChildrenRegistryController::class, "create"]);
	});
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

	Route::view("/administrator/{accessId}{any?}", "administrator")->where("any", ".*")->middleware("employee.role:administrator");
});

Route::redirect("/rejestracja", "/authentication/access-activation/code");
Route::redirect("/", "/myaccount");
