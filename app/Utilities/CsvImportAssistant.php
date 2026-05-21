<?php

namespace App\Utilities;

use App\Exceptions\CsvImportException;
use Illuminate\Http\UploadedFile;

class CsvImportAssistant
{
	/**
	 * Generic function to handle mass imports from CSV files.
	 * @param UploadedFile $file The uploaded CSV file.
	 * @param \Closure $callback A callback function that will be called for each row (typically used for validation).
	 * @return array The validated rows, ready to be inserted into the database.
	 * @throws CsvImportException If there are any validation errors.
	 */
	static function import(UploadedFile $file, \Closure $callback): array {
		$handle = fopen($file->getRealPath(), "r");
		if ($handle === false) {
			throw new CsvImportException(["CANNOT_READ_FILE"]);
		}

		/* We can automatically detect if the file is using commas, semicolons or tabs as a delimiter by
		 reading the first line. Depending on locale settings, MS Excel can switch between commas and tabs.
		I don't think there is any spreadsheet software that uses tabs by default, but TSV is frequently used by
		CKE, and it doesn't hurt to implement. */
		$firstLine = fgets($handle);
		if ($firstLine === false) {
			throw new CsvImportException(["FILE_IS_EMPTY"]);
		}
		if (str_contains($firstLine, "\t")) {
			$separator = "\t";
		} else if (str_contains($firstLine, ",")) {
			$separator = ",";
		} else {
			$separator = ";";
		}
		rewind($handle);

		$headers = fgetcsv($handle, 0, $separator);

		/* Strip UTF-8 BOM if present.
		MS Excel usually exports with a BOM at the beginning of the file, while other software (LibreOffice,
		Google Sheets, Apple Numbers) doesn't. */
		if (str_starts_with($headers[0], "\xEF\xBB\xBF")) {
			$headers[0] = substr($headers[0], 3);
		}
		$headers = array_map("trim", $headers);

		$rowsToInsert = [];
		$errors = [];
		$rowNumber = 2; // 1 is headers

		while (($data = fgetcsv($handle, 0, $separator)) !== false) {
			if (count($headers) !== count($data)) {
				$errors[] = "Rząd $rowNumber: Liczba kolumn nie zgadza się z wymaganą liczbą.";
				$rowNumber++;
				continue;
			}

			$rowData = array_combine($headers, $data);

			// treat empty spaces as nulls
			$rowData = array_map(function ($value) {
				$val = trim($value);
				return $val === "" ? null : $val;
			}, $rowData);

			$callback($rowData, function (string $error) use ($rowNumber, &$errors) {
				$errors[] = "Rząd $rowNumber: $error";
			}, function (array $row) use (&$rowsToInsert) {
				$rowsToInsert[] = $row;
			});

			$rowNumber++;
		}
		fclose($handle);

		if (!empty($errors)) {
			throw new CsvImportException($errors);
		}

		return $rowsToInsert;
	}
}
