<?php

use App\Http\Controllers\AccountAccessesController;
use App\Http\Controllers\AccountLogController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\ChildrenRegistryController;
use App\Http\Controllers\ClassificationPeriodController;
use App\Http\Controllers\ClassUnitController;
use App\Http\Controllers\CompulsoryEducationFulfillmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GradebookController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\PersonController;
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
		Route::post("api/employees/accesses/document", [EmployeeController::class, "generateEmployeeAccessesDocument"]);

		Route::get("/api/subjects", [SubjectController::class, "list"]);
		Route::post("/api/subjects", [SubjectController::class, "create"]);
		Route::put("/api/subjects/{subject}", [SubjectController::class, "update"]);
		Route::put("/api/subjects/{subject}/activity", [SubjectController::class, "archive"]);

		Route::get("/api/classUnits", [ClassUnitController::class, "list"]);
		Route::post("/api/classUnits", [ClassUnitController::class, "create"]);
		Route::get("/api/classUnits/{classUnit}", [ClassUnitController::class, "show"]);
		Route::put("/api/classUnits/{classUnit}", [ClassUnitController::class, "update"]);
		Route::delete("/api/classUnits/{classUnit}", [ClassUnitController::class, "delete"]);

		Route::get("/api/schoolUnits/{schoolUnitId}/classificationPeriods/{schoolYear}", [ClassificationPeriodController::class, "list"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/classificationPeriods/{schoolYear}", [ClassificationPeriodController::class, "save"]);
		Route::delete("/api/schoolUnits/{schoolUnitId}/classificationPeriods/{schoolYear}", [ClassificationPeriodController::class, "delete"]);
	});

	Route::middleware(["employee.role:secretary"])->group(function () {
		Route::post("/api/schoolUnits/{schoolUnitId}/people/lookup", [PersonController::class, "lookup"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/people", [PersonController::class, "create"]);
		Route::post("/api/schoolUnits/{schoolUnitId}/people/import", [PersonController::class, "import"]);
		Route::put("/api/schoolUnits/{schoolUnitId}/people/{person}", [PersonController::class, "update"]);
		Route::get("/api/people/{person}", [PersonController::class, "show"]);
		Route::delete("/api/people/{person}", [PersonController::class, "destroy"]);
		Route::post("/api/people/{person}/guardians", [GuardianController::class, "create"]);

		Route::put("/api/guardians/{guardian}", [GuardianController::class, "update"]);
		Route::delete("/api/guardians/{guardian}", [GuardianController::class, "destroy"]);

		Route::get("/api/studentRegistry", [StudentRegistryController::class, "list"]);
		Route::post("/api/studentRegistry", [StudentRegistryController::class, "create"]);
		Route::get("/api/studentRegistry/{studentRegistry}", [StudentController::class, "list"]);
		Route::post("/api/studentRegistry/{studentRegistry}", [StudentController::class, "create"]);
		Route::get("/api/studentRegistry/{studentRegistry}/export", [StudentRegistryController::class, "export"]);

		Route::put("/api/students/{student}", [StudentController::class, "update"]);
		Route::delete("/api/students/{student}", [StudentController::class, "destroy"]);

		Route::get("/api/childrenRegistry", [ChildrenRegistryController::class, "list"]);
		Route::post("/api/childrenRegistry", [ChildrenRegistryController::class, "create"]);
		Route::get("/api/childrenRegistry/{childrenRegistry}", [ChildController::class, "list"]);
		Route::post("/api/childrenRegistry/{childrenRegistry}", [ChildController::class, "create"]);
		Route::get("/api/childrenRegistry/{childrenRegistry}/export", [ChildrenRegistryController::class, "export"]);
		Route::delete("/api/children/{child}", [ChildController::class, "destroy"]);
		Route::post("/api/children/{child}/fulfillment", [CompulsoryEducationFulfillmentController::class, "create"]);
		Route::put("/api/children/{child}/fulfillment/{fulfillment}", [CompulsoryEducationFulfillmentController::class, "update"]);
		Route::delete("/api/children/{child}/fulfillment/{fulfillment}", [CompulsoryEducationFulfillmentController::class, "destroy"]);
	});

	Route::middleware(["employee.role:administrator,headmaster,teacher"])->group(function () {
		Route::get("/api/schoolUnits/{schoolUnitId}/gradebooks", [GradebookController::class, "list"]);
		Route::post("/api/gradebooks", [GradebookController::class, "create"]);
		Route::get("/api/gradebooks/{gradebook}/students", [GradebookController::class, "listStudents"]);
		Route::post("/api/gradebooks/{gradebook}/students", [GradebookController::class, "attachStudentsToGradebook"]);
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

Route::redirect("/rejestracja", "/authentication/access-activation/code")->name("activateAccess");
Route::view("/authentication/log-in", "index")->name("login");
Route::view("/{any?}", "index")->where("any", ".*");
