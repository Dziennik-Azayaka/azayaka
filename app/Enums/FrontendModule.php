<?php

namespace App\Enums;

enum FrontendModule: string
{
	case ADMINISTRATOR = "administrator";
	case SECRETARY = "secretary";
	case REGISTER = "register";
	case TEACHER = "teacher";
	case STUDENT = "student";
}
