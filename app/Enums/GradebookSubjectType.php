<?php

namespace App\Enums;

/**
 *    The possible types of subjects in a gradebook.
 *    Refer to Rozporządzenie Ministra Edukacji i Nauki z dnia 7 czerwca 2023 r. w sprawie świadectw,
 *    dyplomów państwowych i innych druków, articles 27 and 29
 */
enum GradebookSubjectType: string
{
	// common
	case COMPULSORY = "Obowiązkowy";
	case BILINGUAL_REGULAR_SUBJECT = "Nauczanie dwujęzyczne (nd)";
	case BILINGUAL_LINGUISTIC_SUBJECT = "Poziom dwujęzyczny (pd)";
	// primary school (grades 4-8)

	case PRIMARY_SCHOOL_FIRST_LINGUISTIC_SUBJECT = "II.1.";
	case PRIMARY_SCHOOL_FIRST_BILINGUAL_LINGUISTIC_SUBJECT = "II.1.DJ";
	case PRIMARY_SCHOOL_SECOND_LINGUISTIC_SUBJECT = "II.2.";
	case PRIMARY_SCHOOL_SECOND_BILINGUAL_LINGUISTIC_SUBJECT = "II.2.DJ";
	// trade schools (first level)
	case TRADE_SCHOOL_FRESH_LINGUISTIC_SUBJECT = "III.BS1.0";
	case TRADE_SCHOOL_FIRST_CONTINUED_LINGUISTIC_SUBJECT = "III.BS1.1";
	case TRADE_SCHOOL_SECOND_CONTINUED_LINGUISTIC_SUBJECT = "III.BS1.2";

	// trade school (second level)
	case TRADE_SCHOOL_2_FRESH_LINGUISTIC_SUBJECT = "III.BS2.0";
	case TRADE_SCHOOL_2_FIRST_CONTINUED_LINGUISTIC_SUBJECT = "III.BS2.1";
	case TRADE_SCHOOL_2_SECOND_CONTINUED_LINGUISTIC_SUBJECT = "III.BS2.2";

	// technical schools / high schools
	case SECONDARY_SCHOOL_FIRST_CONTINUED_BASIC_LINGUISTIC_SUBJECT = "III.1.P";
	case SECONDARY_SCHOOL_FIRST_CONTINUED_EXTENDED_LINGUISTIC_SUBJECT = "III.1.R";
	case SECONDARY_SCHOOL_FRESH_LINGUISTIC_SUBJECT = "III.2.0.";
	case SECONDARY_SCHOOL_SECOND_CONTINUED_LINGUISTIC_SUBJECT = "III.2.";
	case SECONDARY_SCHOOL_BILINGUAL_CONTINUED_LINGUISTIC_SUBJECT = "III.DJ";
}
