<?php

namespace App\Documents;

interface DocumentInterface
{
	public function generateDocument(): void;
	public function downloadDocument(): \Illuminate\Http\Response;
	public function streamDocument(): \Illuminate\Http\Response;
	public function saveDocument(): void;
}
