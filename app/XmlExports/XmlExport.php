<?php

namespace App\XmlExports;

use App\Enums\XmlExportType;
use App\Models\ResidenceAddress;
use App\Models\SchoolUnit;
use DOMDocument;
use DOMElement;

/**
 * Abstract class for exporting XML data based on the provided school unit.
 * It defines the essential structure and behaviour for XML export operations.
 */
abstract class XmlExport implements XmlExportInterface
{
	/**
	 * The school unit for which the XML export is being generated.
	 * @var SchoolUnit
	 */
	protected SchoolUnit $schoolUnit;

	/**
	 * The DOMDocument instance used for generating the XML.
	 * @var DOMDocument
	 */
	protected DOMDocument $dom;

	/**
	 * The root element of the XML document.
	 * @var DOMElement
	 */
	protected DOMElement $rootElement;

	/**
	 * The section element within the root element, named after a specific XMLExportType.
	 * @var DOMElement
	 */
	protected DOMElement $sectionNode;

	/**
	 * Returns the XmlExportType for the current export operation.
	 * @return XmlExportType
	 */
	abstract protected function getExportType(): XmlExportType;

	public function __construct(SchoolUnit $schoolUnit)
	{
		$this->schoolUnit = $schoolUnit;
		$this->dom = new DOMDocument("1.0", "UTF-8");
		$this->dom->formatOutput = true;
		$schemaBaseUrl = config("app.url") . "/xml-schemas";

		$isRegister = match ($this->getExportType()) {
			XmlExportType::ATTENDEE_REGISTER, XmlExportType::CHILDREN_REGISTER,
			XmlExportType::PUPIL_REGISTER, XmlExportType::STUDENTS_REGISTER => true,
			default => false,
		};

		if ($isRegister) {
			$xlst = $this->dom->createProcessingInstruction("xml-stylesheet", "type='text/xsl' href='$schemaBaseUrl/schematEksportuKsiegi.xsl'");
			$this->rootElement = $this->dom->createElement("Ksiega");
			$this->rootElement->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			$this->rootElement->setAttribute("xsi:noNamespaceSchemaLocation", "$schemaBaseUrl/schematKsiegi.xsd");
		} else {
			$xlst = $this->dom->createProcessingInstruction("xml-stylesheet", "type='text/xsl' href='$schemaBaseUrl/xml-schemas/schematEksportuDziennika.xsl'");
			$this->rootElement = $this->dom->createElement("Dziennik");
			$this->rootElement->setAttribute("xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
			$this->rootElement->setAttribute("xsi:noNamespaceSchemaLocation", "$schemaBaseUrl/schematDziennika.xsd");
		}
		$this->dom->appendChild($xlst);
		$this->dom->appendChild($this->rootElement);

		$this->sectionNode = $this->dom->createElement($this->getExportType()->value);
		$this->rootElement->appendChild($this->sectionNode);
		$this->buildSchoolData();
	}

	/**
	 * Creates a new DOM element with the specified name and value, then appends it to the section node.
	 * If the value is null, the element is not created.
	 * @param string $name The element's name
	 * @param string $value The value inside the element
	 * @return void
	 * @throws \DOMException
	 */
	protected function addElement(string $name, string $value): void
	{
		if ($value != null) {
			$this->sectionNode->appendChild($this->dom->createElement($name, htmlspecialchars($value)));
		}
	}

	/**
	 * Builds the school data section required in every XML document,
	 * including school name, address, and other relevant information.
	 * See ksiegaEwidencjiDzieciTyp and for more details.
	 * @return void
	 * @throws \DOMException
	 */
	private function buildSchoolData(): void
	{
		$this->addElement("NazwaPlacowki", $this->schoolUnit->name);
		$addressElement = $this->dom->createElement("AdresPlacowki");
		$addressElement->appendChild($this->dom->createElement("KodPocztowy", $this->schoolUnit->postal_code));
		$addressElement->appendChild($this->dom->createElement("Miejscowosc", $this->schoolUnit->town));
		$addressElement->appendChild($this->dom->createElement("Ulica", $this->schoolUnit->street));
		$addressElement->appendChild($this->dom->createElement("NumerDomu", $this->schoolUnit->house_number));
		$addressElement->appendChild($this->dom->createElement("NumerLokalu", $this->schoolUnit->flat_number ?? "")); // An empty field should be created if no flat number
		$this->sectionNode->appendChild($addressElement);
		$this->addElement("Wojewodztwo", $this->schoolUnit->voivodeship->name);
		$this->addElement("Gmina", $this->schoolUnit->municipality);
	}

	/**
	 * Builds the residence address data for a given DOMElement. The "adresTyp" is shared between all export types.
	 * @param DOMElement $DOMElement The DOMElement to which the address data will be appended
	 * @param ResidenceAddress $residenceAddress The ResidenceAddress object containing the address data
	 * @return void
	 * @throws \DOMException
	 */
	protected function buildResidenceAddressData(DOMElement $DOMElement, ResidenceAddress $residenceAddress): void
	{
		$addressElement = $this->dom->createElement("Adres");
		$addressElement->appendChild($this->dom->createElement("KodPocztowy", $residenceAddress->postal_code));
		$addressElement->appendChild($this->dom->createElement("Miejscowosc", $residenceAddress->town));
		$addressElement->appendChild($this->dom->createElement("Ulica", $residenceAddress->street));
		$addressElement->appendChild($this->dom->createElement("NumerDomu", $residenceAddress->house_number));
		$addressElement->appendChild($this->dom->createElement("NumerLokalu", $residenceAddress->flat_number ?? ""));
		$DOMElement->appendChild($addressElement);
	}


	/**
	 * Generates an XML document from the DOMDocument instance.
	 * @return string The generated XML string
	 */
	public function generateXml(): string
	{
		return $this->dom->saveXML();
	}

	/**
	 * Generates an HTML document from the DOMDocument instance using XSLT.
	 * @return string The generated HTML string
	 */
	public function generateHtml(): string
	{
		$xsl = new DOMDocument();
		$xsl->load(public_path("xml-schemas/schematEksportuKsiegi.xsl"));
		$xsltProcessor = new \XSLTProcessor();
		$xsltProcessor->importStyleSheet($xsl);
		return $xsltProcessor->transformToXML($this->dom);
	}
}
