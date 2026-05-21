<?php

namespace App\XmlExports;

use Illuminate\Http\Response;

interface XmlExportInterface
{
	public function generateXml(): string;
	public function downloadXml(): Response;
	public function generateHtml(): string;
	public function downloadHtml(): Response;
}
