<?php

namespace App\Enums;

enum AccountEventType: string
{
	case FAILED_LOGIN_ATTEMPT = "failed_login_attempt";
	case SUCCESSFUL_LOGIN_ATTEMPT = "successful_login_attempt";
	case LOGOUT = "logout";
	case LOGGED_OUT_BY_ANOTHER_DEVICE = "logged_out_by_another_device";
	case CREDENTIALS_CHANGED = "credentials_changed";
}
