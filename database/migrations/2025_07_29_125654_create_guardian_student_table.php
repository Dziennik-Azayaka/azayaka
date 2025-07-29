<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("guardian_student", function (Blueprint $table) {
            $table->id();
			$table->foreignId("guardian_id")->constrained("guardians")->onDelete("cascade");
			$table->foreignId("student_id")->constrained("students")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("guardians_students");
    }
};
