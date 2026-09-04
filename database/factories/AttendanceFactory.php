<?php

namespace Database\Factories;

use App\Enums\AttendancePrimitiveType;
use App\Models\Attendance;
use App\Models\AttendanceComplexType;
use App\Models\Employee;
use App\Models\Lesson;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "student_id" => Student::factory(),
			"lesson_id" => Lesson::factory(),
			"attendance_complex_type_id" => AttendanceComplexType::factory(),
			"employee_id" => Employee::factory()
        ];
    }
}
