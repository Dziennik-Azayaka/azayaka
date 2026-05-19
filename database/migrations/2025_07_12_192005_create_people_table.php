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
        Schema::create("people", function (Blueprint $table) {
            $table->id();
			$table->string("first_name");
			$table->string("last_name");
			$table->string("second_name")->nullable();
			$table->string("pesel")->nullable();
			$table->string("alternate_identity_document")->nullable();
			$table->date("birthdate");
			$table->string("birthplace")->nullable();
			$table->string("gender")->nullable();
			$table->string("last_modified_by")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("people");
    }
};
