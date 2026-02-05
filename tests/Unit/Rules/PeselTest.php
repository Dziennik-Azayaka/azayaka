<?php

namespace Tests\Unit\Rules;

use App\Rules\Pesel;
use Tests\TestCase;
use Validator;

class PeselTest extends TestCase
{
	public function test_valid_pesel_passes(): void
	{
		$validator = Validator::make(["pesel" => "12345678903"], ["pesel" => new Pesel]);
		$this->assertTrue($validator->passes());
	}

	public function test_invalid_pesel_fails(): void
	{
		$validator = Validator::make(["pesel" => "12345678901"], ["pesel" => new Pesel]);
		$this->assertFalse($validator->passes());
	}

	public function test_invalid_length_pesel_fails(): void
	{
		$validator = Validator::make(["pesel" => "1234567890"], ["pesel" => new Pesel]);
		$this->assertFalse($validator->passes());
	}

	public function test_invalid_characters_pesel_fails(): void
	{
		$validator = Validator::make(["pesel" => "abcdefghijk"], ["pesel" => new Pesel]);
		$this->assertFalse($validator->passes());
	}
}
