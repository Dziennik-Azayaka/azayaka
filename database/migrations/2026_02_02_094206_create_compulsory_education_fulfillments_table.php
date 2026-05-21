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
			$table->foreignId("child_id")->constrained("children")->onDelete("cascade");
			$table->integer("school_year");
			$table->date("control_date");
			$table->string("kindergarten_info")->nullable();
			$table->string("postponement_info")->nullable();
			$table->string("school_info")->nullable();
			$table->string("out_of_school_info")->nullable();
			$table->integer("level");
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
