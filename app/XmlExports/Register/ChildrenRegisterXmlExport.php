<?php

namespace App\XmlExports\Register;

use App\Enums\XmlExportType;
use App\Models\Child;
use App\Models\ChildrenRegistry;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\XmlExports\Register\RegisterXmlExport;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ChildrenRegisterXmlExport extends RegisterXmlExport
{
	protected \DOMElement $childrenElement;

	/**
	 * @inheritDoc
	 */
	protected function getExportType(): XmlExportType
	{
		return XmlExportType::CHILDREN_REGISTER;
	}

	public function __construct(SchoolUnit $schoolUnit, Carbon|string $date, ChildrenRegistry $childrenRegistry)
	{
		parent::__construct($schoolUnit, $date);
		$this->childrenElement = $this->dom->createElement("Dzieci");
		$this->sectionNode->appendChild($this->childrenElement);
		foreach ($childrenRegistry->children()->with(["person", "person.residenceAddress", "compulsoryEducationFulfillments"])->get() as $child) {
			$this->addChild($child);
		}
	}

	private function addChild(Child $child): void
	{
		$studentElement = $this->dom->createElement("Dziecko");
		$studentElement->setAttribute("id", $child->id);
		$studentElement->appendChild($this->dom->createElement("Numer", $child->id));
		$studentElement->appendChild($this->dom->createElement("Imie", $child->person->first_name));
		if ($child->person->second_name) {
			$studentElement->appendChild($this->dom->createElement("DrugieImie", $child->person->second_name));
		}
		$studentElement->appendChild($this->dom->createElement("Nazwisko", $child->person->last_name));
		$studentElement->appendChild($this->dom->createElement(
			"DataUrodzenia", Carbon::parse($child->person->birthdate)->format("Y-m-d")));
		if ($child->person->birthplace) {
			$studentElement->appendChild($this->dom->createElement("MiejsceUrodzenia", $child->person->birthplace));
		}
		if ($child->person->pesel) {
			$studentElement->appendChild($this->dom->createElement("Pesel", $child->person->pesel));
		} else {
			$studentElement->appendChild($this->dom->createElement(
				"NazwaINumerDokumentuPotwierdzajacegoTozsamosc", $child->person->alternate_identity_document));
		}
		$this->buildResidenceAddressData($studentElement, $child->person->residenceAddress);
		$guardiansElement = $this->dom->createElement("Rodzice");
		$child->person->guardians->each(function ($guardian) use ($guardiansElement) {
			$parentElement = $this->dom->createElement("Rodzic");
			$parentElement->setAttribute("id", $guardian->id);
			$parentElement->appendChild($this->dom->createElement("Imie", $guardian->first_name));
			$parentElement->appendChild($this->dom->createElement("Nazwisko", $guardian->last_name));
			$this->buildResidenceAddressData($parentElement, $guardian->residenceAddress);
			$guardiansElement->appendChild($parentElement);
		});
		$studentElement->appendChild($guardiansElement);

		$latestCompulsoryEducationFulfillmentEntry = $child
			->compulsoryEducationFulfillments()
			->orderByDesc("school_year")->first();

		if ($latestCompulsoryEducationFulfillmentEntry) {
			if ($latestCompulsoryEducationFulfillmentEntry->kindergarten_info) {
				$studentElement->appendChild($this->dom->createElement(
					"InformacjeOPrzedszkolu", $latestCompulsoryEducationFulfillmentEntry->kindergarten_info
				));
			}
			if ($latestCompulsoryEducationFulfillmentEntry->postponement_info) {
				$studentElement->appendChild($this->dom->createElement(
					"InformacjeOOdroczeniu", $latestCompulsoryEducationFulfillmentEntry->postponement_info
				));
			}
			if ($latestCompulsoryEducationFulfillmentEntry->school_info) {
				$studentElement->appendChild($this->dom->createElement(
					"InformacjeOSzkoleLubMiejscuRealizacjiZajecRewalidacyjnoWychowawczych",
					$latestCompulsoryEducationFulfillmentEntry->school_info
				));
			}
			if ($latestCompulsoryEducationFulfillmentEntry->out_of_school_info) {
				$studentElement->appendChild($this->dom->createElement(
					"InformacjeOSpelnianiuPrzezDzieckoObowiazkuSzkolnegoPozaSzkola",
					$latestCompulsoryEducationFulfillmentEntry->out_of_school_info
				));
			}
		}

		$this->childrenElement->appendChild($studentElement);
	}
}
