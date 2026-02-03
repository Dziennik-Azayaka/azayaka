<?php

namespace App\Http\Controllers;

use App\Models\StudentRegistry;
use Illuminate\Http\Request;

class StudentRegistryController extends Controller
{
	public function list()
	{
		return StudentRegistry::all();
	}
}
