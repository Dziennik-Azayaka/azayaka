<?php

namespace App\XmlExports\Register;

use App\Enums\XmlExportType;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\StudentRegistry;
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

	public function __construct(SchoolUnit $schoolUnit, Carbon|string $date, StudentRegistry $studentRegistry)
	{
		parent::__construct($schoolUnit, $date);
		$this->studentsElement = $this->dom->createElement("Uczniowie");
		$this->sectionNode->appendChild($this->studentsElement);
		foreach ($studentRegistry->students()->with(["person", "person.residenceAddress"])->get() as $student) {
			$this->addStudent($student);
		}
	}

	private function addStudent(Student $student): void
	{
		$studentElement = $this->dom->createElement("Uczen");
		$studentElement->setAttribute("id", $student->id);
		$studentElement->appendChild($this->dom->createElement("Numer", $student->id));
		$studentElement->appendChild($this->dom->createElement("Imie", $student->person->first_name));
		if ($student->person->second_name) {
			$studentElement->appendChild($this->dom->createElement("DrugieImie", $student->person->second_name));
		}
		$studentElement->appendChild($this->dom->createElement("Nazwisko", $student->person->last_name));
		$studentElement->appendChild($this->dom->createElement(
			"DataUrodzenia", Carbon::parse($student->person->birthdate)->format("Y-m-d")));
		if ($student->person->birthplace) {
			$studentElement->appendChild($this->dom->createElement("MiejsceUrodzenia", $student->person->birthplace));
		}
		if ($student->person->pesel) {
			$studentElement->appendChild($this->dom->createElement("Pesel", $student->person->pesel));
		} else {
			$studentElement->appendChild($this->dom->createElement(
				"NazwaINumerDokumentuPotwierdzajacegoTozsamosc", $student->person->alternate_identity_document));
		}
		$this->buildResidenceAddressData($studentElement, $student->person->residenceAddress);
		$guardiansElement = $this->dom->createElement("Rodzice");
		$student->person->guardians->each(function ($guardian) use ($guardiansElement) {
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

		$currentClassUnit = $student->gradebooks()->withPivot("date_from", "date_to")
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
