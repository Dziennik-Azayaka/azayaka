<?php

namespace App\XmlExports;

interface XmlExportInterface
{
	public function generateXml(): string;
	//public function downloadXml(): \Illuminate\Http\Response;
	public function generateHtml(): string;
	//public function downloadHtml(): \Illuminate\Http\Response;
}
