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
        Schema::create("student_registries", function (Blueprint $table) {
            $table->id();
			$table->foreignId("school_unit_id");
            $table->timestamps();
        });

		Schema::create("student_registry_student", function (Blueprint $table) {
			$table->id();
			$table->foreignId("student_registry_id")->constrained("student_registries")->onDelete("cascade");
			$table->foreignId("student_id")->constrained("students")->onDelete("cascade");
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("student_registries");
    }
};
