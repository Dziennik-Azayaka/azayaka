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
        Schema::create("compulsory_education_fulfillments", function (Blueprint $table) {
            $table->id();
			$table->foreignId("student_id")->constrained("students")->onDelete("cascade");
			$table->foreignId("children_registry_id")->constrained("children_registries")->onDelete("cascade");
			$table->integer("school_year");
			$table->date("control_date");
			$table->string("fulfillment_form");
			$table->integer("level");
			$table->string("relationship");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("compulsory_education_fulfillments");
    }
};
