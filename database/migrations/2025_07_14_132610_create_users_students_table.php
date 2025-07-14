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
        Schema::create("users_students", function (Blueprint $table) {
            $table->id();
			$table->foreignId("user_id")->constrained("users")->cascadeOnDelete();
			$table->foreignId("student_id")->constrained("students")->cascadeOnDelete();
			$table->enum("acts_as", ["student", "guardian"])->default("student");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("users_students");
    }
};
