<?php

namespace App\XmlExports\Register;

use App\Enums\XmlExportType;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\XmlExports\Register\RegisterXmlExport;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentsRegisterXmlExport extends RegisterXmlExport
{
	protected \DOMElement $studentsElement;

	/**
	 * @inheritDoc
	 */
	protected function getExportType(): XmlExportType
	{
		return XmlExportType::STUDENTS_REGISTER;
	}

	public function __construct(SchoolUnit $schoolUnit, Carbon|string $date, Collection|array $students)
	{
		parent::__construct($schoolUnit, $date);
		$this->studentsElement = $this->dom->createElement("Uczniowie");
		$this->sectionNode->appendChild($this->studentsElement);
		foreach ($students as $student) {
			$this->addStudent($student);
		}
	}

	private function addStudent(Student $student): void
	{
		$studentElement = $this->dom->createElement("Uczen");
		$studentElement->setAttribute("id", $student->id);
		$studentElement->appendChild($this->dom->createElement("Numer", $student->id));
		$studentElement->appendChild($this->dom->createElement("Imie", $student->first_name));
		if ($student->second_name) {
			$studentElement->appendChild($this->dom->createElement("DrugieImie", $student->second_name));
		}
		$studentElement->appendChild($this->dom->createElement("Nazwisko", $student->last_name));
		$studentElement->appendChild($this->dom->createElement(
			"DataUrodzenia", Carbon::parse($student->birthdate)->format("Y-m-d")));
		if ($student->birthplace) {
			$studentElement->appendChild($this->dom->createElement("MiejsceUrodzenia", $student->birthplace));
		}
		if ($student->pesel) {
			$studentElement->appendChild($this->dom->createElement("Pesel", $student->pesel));
		} else {
			$studentElement->appendChild($this->dom->createElement(
				"NazwaINumerDokumentuPotwierdzajacegoTozsamosc", $student->alternate_identity_document));
		}
		$this->buildResidenceAddressData($studentElement, $student->residenceAddress);
		$guardiansElement = $this->dom->createElement("Rodzice");
		$student->guardians->each(function ($guardian) use ($guardiansElement) {
			$parentElement = $this->dom->createElement("Rodzic");
			$parentElement->setAttribute("id", $guardian->id);
			$parentElement->appendChild($this->dom->createElement("Imie", $guardian->first_name));
			$parentElement->appendChild($this->dom->createElement("Nazwisko", $guardian->last_name));
			$this->buildResidenceAddressData($parentElement, $guardian->residenceAddress);
			$guardiansElement->appendChild($parentElement);
		});
		$studentElement->appendChild($guardiansElement);
		$studentElement->appendChild($this->dom->createElement(
			"DataRozpoczeciaNauki",
			Carbon::parse($student->admission_date)->format("d.m.Y") . " r."));

		$currentClassUnit = $student->classUnits()->withPivot("date_from", "date_to")
			->get()->sortBy("pivot.date_to")->last();

		if ($currentClassUnit) {
			$studentElement->appendChild(
				$this->dom->createElement(
					"Oddzial",
					$currentClassUnit->current_level . $currentClassUnit->mark));
		}

		$this->studentsElement->appendChild($studentElement);
	}
}
