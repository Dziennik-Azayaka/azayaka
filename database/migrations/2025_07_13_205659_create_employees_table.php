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
        Schema::create("employees", function (Blueprint $table) {
            $table->id();
			$table->string("first_name");
			$table->string("last_name");
			$table->string("shortcut")->unique();
			$table->boolean("active")->default(true);
			$table->boolean("is_admin")->default(false);
			$table->boolean("is_headmaster")->default(false);
			$table->boolean("is_secretary")->default(false);
			$table->boolean("is_teacher")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("employees");
    }
};
