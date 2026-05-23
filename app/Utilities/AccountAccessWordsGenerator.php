<?php

namespace App\Utilities;

class AccountAccessWordsGenerator
{
	static function generate(): string
	{
		$words = explode("\n", file_get_contents(resource_path("data/dictionary.txt")));
		$keys = array_rand($words, 10);
		$oneTimeWords = "";
		foreach ($keys as $key) {
			$oneTimeWords .= $words[$key] . ",";
		}
		return rtrim($oneTimeWords, ",");
	}
}
