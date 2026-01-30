<?php

namespace Database\Seeders;

use App\Models\AccountAccess;
use App\Models\AccountLog;
use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\Guardian;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\Employee;
use App\Models\Subject;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		// User::factory(10)->create();

		$rootUser = User::factory()->create([
			"name" => "Test User",
			"email" => "test@example.com",
		]);

		Student::factory(10)->create();
		Guardian::factory(10)->create();
		Employee::factory(10)->create();
		AccountAccess::factory(10)->create();

		$rootEmployee = Employee::factory()->create([
			"is_headmaster" => true,
			"is_admin" => true,
		]);
		AccountAccess::factory()->create([
			"employee_id" => $rootEmployee->id,
			"user_id" => $rootUser->id,
		]);

		AccountLog::factory(20)->create();

		SchoolComplex::factory(1)->create();
		SchoolUnit::factory(5)->create();

		Subject::factory(5)->create();
		ClassUnit::factory(15)->create();

		ClassificationPeriod::create([
			"school_unit_id" => 1,
			"school_year" => 2024,
			"period_number" => 1,
			"period_start" => "2024-09-01",
			"period_end" => "2025-01-30"
		]);

		ClassificationPeriod::create([
			"school_unit_id" => 1,
			"school_year" => 2024,
			"period_number" => 2,
			"period_start" => "2025-02-01",
			"period_end" => "2025-08-31"
		]);
	}
}
