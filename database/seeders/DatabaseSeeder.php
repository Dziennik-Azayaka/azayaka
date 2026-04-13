<?php

namespace Database\Seeders;

use App\Models\AccountAccess;
use App\Models\AccountLog;
use App\Models\ChildrenRegistry;
use App\Models\ClassificationPeriod;
use App\Models\ClassUnit;
use App\Models\CompulsoryEducationFulfillment;
use App\Models\Guardian;
use App\Models\ResidenceAddress;
use App\Models\SchoolComplex;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\Employee;
use App\Models\StudentRegistry;
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

		ResidenceAddress::factory(10)->create();

		$studentRegistry = StudentRegistry::create([
			"school_unit_id" => 1,
			"created_at" => "2024-09-01"
		]);

		$childrenRegistry = ChildrenRegistry::create([
			"school_unit_id" => 1,
			"created_at" => "2024-09-01"
		]);
		$students = Student::factory(10)->create([
			"student_registry_id" => $studentRegistry->id,
			"children_registry_id" => $childrenRegistry->id
		]);
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
		$schoolUnits = SchoolUnit::factory(4)->create([
			"school_complex_id" => 1,
		]);

		$classificationPeriods = [];
		$currentYear = now()->year;
		$schoolUnits->each(function ($schoolUnit) use ($currentYear, &$classificationPeriods) {
			for ($i = $currentYear - 10; $i < $currentYear + 10; $i++) {
				$nextYear = $i + 1;
				$classificationPeriods[] = [
					"school_unit_id" => $schoolUnit->id,
					"school_year" => $i,
					"period_number" => 1,
					"period_start" => "$i-09-01",
					"period_end" => "$nextYear-02-01",
				];
				$classificationPeriods[] = [
					"school_unit_id" => $schoolUnit->id,
					"school_year" => $i,
					"period_number" => 2,
					"period_start" => "$nextYear-02-02",
					"period_end" => "$nextYear-08-31",
				];
			}
		});
		ClassificationPeriod::insert($classificationPeriods);

		$genericStartingClassificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => 1,
			"school_year" => 2024,
			"period_number" => 1,
			"period_start" => "2024-09-01",
			"period_end" => "2025-01-30"
		]);

		Subject::factory(5)->create();
		$classUnits = ClassUnit::factory(15)
			->recycle($schoolUnits)
			->recycle($genericStartingClassificationPeriod)
			->create();


		$genericEndingClassificationPeriod = ClassificationPeriod::create([
			"school_unit_id" => 1,
			"school_year" => 2024,
			"period_number" => 2,
			"period_start" => "2025-02-01",
			"period_end" => "2025-08-31"
		]);

		$classUnits->each(function ($classUnit) use ($genericStartingClassificationPeriod, $genericEndingClassificationPeriod) {
			$classUnit->periods()->sync([
				$genericStartingClassificationPeriod->id => ["level" => rand(1, 8)],
				$genericEndingClassificationPeriod->id => ["level" => rand(1, 8)],
			]);
		});

		CompulsoryEducationFulfillment::factory(10)->recycle($students)->create();
	}
}
