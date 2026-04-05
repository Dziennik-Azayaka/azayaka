<?php

namespace App\Documents;

use Barryvdh\DomPDF\PDF;

trait BasicPDFFunctions
{
	abstract public function getDomPDF() : PDF;
	abstract public function getFilename() : string;
	public function streamDocument(): \Illuminate\Http\Response
	{
		return $this->getDomPDF()->stream($this->getFilename());
	}

	public function downloadDocument(): \Illuminate\Http\Response
	{
		return $this->getDomPDF()->download($this->getFilename());
	}

	public function saveDocument(): void
	{
		$this->getDomPDF()->save(storage_path("app/public/documents/" . $this->getFilename()));
	}
}
