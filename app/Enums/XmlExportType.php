<?php

namespace App\Enums;

/**
 * Enum representing the different types of XML exports.
 * Backed by the section name in the exported document. (the Dziennik ComplexType or Ksiega ComplexType)
 */
enum XmlExportType: string
{
	// Registers (Księgi)
	case CHILDREN_REGISTER = "EwidencjiDzieci";
	case STUDENTS_REGISTER = "Uczniow";
	case ATTENDEE_REGISTER = "Sluchaczy";
	case PUPIL_REGISTER = "Wychowankow";

	// Gradebooks (Dzienniki)
	case KINDERGARTEN_GRADEBOOK = "Przedszkola";
	case SCHOOL_GRADEBOOK = "Szkoly";
	case DAYCARE_GRADEBOOK = "Swietlicy";
	case EDUCATIONAL_CENTER_GRADEBOOK = "OsrodkaWychowawczego";
	case OTHER_CLASSES_GRADEBOOK = "InnychZajec";
	case OTHER_CLASSES_PSYCHOLOGICAL_PEDAGOGICAL_SUPPORT_GRADEBOOK = "InnychZajecPomocPsychologicznoPedagogiczna";
	case REVALIDATION_EDUCATIONAL_CLASSES_GRADEBOOK = "ZajecRewalidacyjnoWychowawczych";
	case PEDAGOGUE_PSYCHOLOGIST_SPEECH_THERAPIST_GRADEBOOK = "PedagogPsychologLogopeda";
	case PERMANENT_CLASSES_GRADEBOOK = "ZajecStalych";
	case PERIODIC_OR_OCCASIONAL_CLASSES_GRADEBOOK = "ZajecOkresowychLubOkazjonalnych";
}
