<?php

namespace App\Enums;

enum AttendancePrimitiveType: int
{
    case PRESENCE = 0;
	case ABSENCE = 1;
	case EXCUSED_ABSENCE = 2;
	case LATENESS = 3;
	case EXCUSED_LATENESS = 4;
	case EXEMPTION = 5;
}
