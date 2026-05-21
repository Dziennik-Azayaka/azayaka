<?php

namespace App\XmlExports\Register;

use App\Enums\XmlExportType;
use App\Models\SchoolUnit;
use App\XmlExports\XmlExport;
use Carbon\Carbon;

abstract class RegisterXmlExport extends XmlExport
{

	public function __construct(SchoolUnit $schoolUnit, Carbon|string $date)
	{
		parent::__construct($schoolUnit);
		if ($date instanceof Carbon) {
			$date = $date->format("d.m.Y");
		}
		$this->addElement("DataZalozeniaEwidencji", $date);
	}
}
