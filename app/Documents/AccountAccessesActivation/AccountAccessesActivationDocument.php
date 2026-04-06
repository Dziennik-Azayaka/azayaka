<?php

namespace App\Documents\AccountAccessesActivation;

use App\Documents\BasicPDFFunctions;
use App\Documents\DocumentInterface;
use App\Enums\AccessType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

final class AccountAccessesActivationDocument implements DocumentInterface
{
	use BasicPDFFunctions;

	protected \Barryvdh\DomPDF\PDF $pdf;
	protected string $filename;
	protected array $accesses = [];

	public function getDomPDF(): \Barryvdh\DomPDF\PDF
	{
		return $this->pdf;
	}

	public function getFilename(): string
	{
		return $this->filename;
	}

	public function __construct()
	{
		$uuid = Str::orderedUuid();
		$this->filename = "Instrukcja_aktywacji_dostępu_$uuid.pdf";
	}

	public function addAccess(AccessType $accessType, string $name, array $code): void {
		$this->accesses[] = new AccountAccessesElement($accessType, $name, $code);
	}

	public function generateDocument(): void
	{
		$this->pdf = Pdf::loadView("documents.account_accesses", [
			"accesses" => $this->accesses
		]);
	}
}
