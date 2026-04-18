<?php

namespace Tests\Unit\Utilities;

use App\Utilities\CaseConverter;
use Illuminate\Support\Collection;
use stdClass;
use Tests\TestCase;

final class CaseConverterTest extends TestCase
{
	public function test_to_camel_case_converts_simple_array(): void
	{
		$array = ["first_name" => "John", "last_name" => "Doe"];
		$expected = ["firstName" => "John", "lastName" => "Doe"];

		$this->assertEquals($expected, CaseConverter::toCamelCase($array));
	}

	public function test_to_snake_case_converts_simple_array(): void
	{
		$array = ["firstName" => "John", "lastName" => "Doe"];
		$expected = ["first_name" => "John", "last_name" => "Doe"];

		$this->assertEquals($expected, CaseConverter::toSnakeCase($array));
	}

	public function test_numeric_keys_are_preserved(): void
	{
		$array = [0 => "first", 1 => "second", "user_id" => 3];
		$expectedCamel = [0 => "first", 1 => "second", "userId" => 3];
		$expectedSnake = [0 => "first", 1 => "second", "user_id" => 3];

		$this->assertEquals($expectedCamel, CaseConverter::toCamelCase($array));
		$this->assertEquals($expectedSnake, CaseConverter::toSnakeCase($expectedCamel));
	}

	public function test_to_camel_case_handles_nested_arrays(): void
	{
		$array = [
			"user_data" => [
				"first_name" => "John",
				"contact_info" => [
					"phone_number" => "123456789"
				]
			]
		];

		$expected = [
			"userData" => [
				"firstName" => "John",
				"contactInfo" => [
					"phoneNumber" => "123456789"
				]
			]
		];

		$this->assertEquals($expected, CaseConverter::toCamelCase($array));
	}

	public function test_to_snake_case_handles_nested_arrays(): void
	{
		$array = [
			"userData" => [
				"firstName" => "John",
				"contactInfo" => [
					"phoneNumber" => "123456789"
				]
			]
		];

		$expected = [
			"user_data" => [
				"first_name" => "John",
				"contact_info" => [
					"phone_number" => "123456789"
				]
			]
		];

		$this->assertEquals($expected, CaseConverter::toSnakeCase($array));
	}

	public function test_handles_support_collections_correctly(): void
	{
		$collection = new Collection(["first_name" => "John"]);

		$expectedCamel = ["firstName" => "John"];
		$this->assertEquals($expectedCamel, CaseConverter::toCamelCase($collection));

		$collectionSnake = new Collection(["firstName" => "John"]);

		$expectedSnake = ["first_name" => "John"];
		$this->assertEquals($expectedSnake, CaseConverter::toSnakeCase($collectionSnake));
	}

	public function test_handles_objects_correctly(): void
	{
		$object = new stdClass();
		$object->first_name = "John";
		$object->last_name = "Doe";

		$array = ["user_profile" => $object];
		$expected = [
			"userProfile" => [
				"firstName" => "John",
				"lastName" => "Doe"
			]
		];

		$this->assertEquals($expected, CaseConverter::toCamelCase($array));
	}
}
