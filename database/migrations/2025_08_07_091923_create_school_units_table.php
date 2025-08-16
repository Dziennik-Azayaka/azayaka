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
			$table->enum("studentCategory", ["childrenAndAdults", "adultsOnly"]);
			$table->string("municipality");
			$table->string("voivodeship");
			$table->string("district")->nullable();
			$table->foreignId("school_complex_id")->nullable()->constrained("school_complexes")->cascadeOnDelete();
            $table->timestamps();
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
