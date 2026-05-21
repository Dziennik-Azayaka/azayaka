<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
	protected function isConnectedDatabaseCompatible(): bool
	{
		$defaultConnection = config("database.default");
		$defaultConnectionDriver = config("database.connections.$defaultConnection.driver");
		if ($defaultConnectionDriver !== "mariadb") {
			if ($defaultConnectionDriver == "sqlsrv") {
				$mssqlMessage = "Microsoft SQL Server supports system versioned tables, but it has not been tested. Please switch to MariaDB for full support.";
				Log::warning($mssqlMessage);
				echo "\n[WARNING] " . $mssqlMessage . "\n";
			} else {
				$genericMessage = "Current database driver $defaultConnectionDriver does not support system versioned tables. Please switch to MariaDB for full support.";
				Log::error($genericMessage);
				echo "\n[ERROR] " . $genericMessage . "\n";
			}
			return false;
		}
		return true;
	}

	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		if (!$this->isConnectedDatabaseCompatible()) return;
		DB::statement("ALTER TABLE residence_addresses ADD SYSTEM VERSIONING;");
		DB::statement("ALTER TABLE students ADD SYSTEM VERSIONING;");
		DB::statement("ALTER TABLE guardians ADD SYSTEM VERSIONING;");
		DB::statement("ALTER TABLE compulsory_education_fulfillments ADD SYSTEM VERSIONING;");
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		if (!$this->isConnectedDatabaseCompatible()) return;
		DB::statement("ALTER TABLE residence_addresses DROP SYSTEM VERSIONING;");
		DB::statement("ALTER TABLE students DROP SYSTEM VERSIONING;");
		DB::statement("ALTER TABLE guardians DROP SYSTEM VERSIONING;");
		DB::statement("ALTER TABLE compulsory_education_fulfillments DROP SYSTEM VERSIONING;");
	}
};
