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
        Schema::create("school_units", function (Blueprint $table) {
            $table->id();
			$table->string("name");
			$table->integer("type");
			$table->boolean("active")->default(true);
			$table->enum("student_category", ["childrenAndYouths", "adultsOnly"]);
			$table->string("municipality"); // Gmina
			$table->integer("voivodeship");
			$table->string("town");
			$table->string("district")->nullable();
			$table->string("short_name");
			$table->string("postal_code");
			$table->string("street");
			$table->string("house_number");
			$table->string("flat_number")->nullable();
			$table->foreignId("school_complex_id")->nullable()->constrained("school_complexes")->cascadeOnDelete();
            $table->timestamps();
        });

		Schema::table("people", function (Blueprint $table) {
			$table->foreignId("school_unit_id")->constrained("school_units")->cascadeOnDelete();
		});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("school_units");
    }
};
