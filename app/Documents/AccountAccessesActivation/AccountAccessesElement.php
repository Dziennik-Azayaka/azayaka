<?php

namespace App\Documents\AccountAccessesActivation;

use App\Enums\AccessType;

class AccountAccessesElement
{
	public AccessType $accessType;
	public string $name;
	public array $code;

	public function __construct(AccessType $accessType, string $name, array $code)
	{
		$this->accessType = $accessType;
		$this->name = $name;
		$this->code = $code;
	}

	public function getAccessTypeName(): string
	{
		return match ($this->accessType) {
			AccessType::STUDENT => "Uczeń",
			AccessType::PARENT => "Rodzic",
			AccessType::EMPLOYEE => "Pracownik"
		};
	}
}
