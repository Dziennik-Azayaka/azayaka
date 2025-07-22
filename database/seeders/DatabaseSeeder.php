<?php

namespace Database\Seeders;

use App\Models\AccountAccess;
use App\Models\Student;
use App\Models\Employee;
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

		User::factory()->create([
			"name" => "Test User",
			"email" => "test@example.com",
		]);

		Student::factory(10)->create();
		Employee::factory(10)->create();
		AccountAccess::factory(10)->create();
	}
}
