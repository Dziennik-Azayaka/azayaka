<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityAlreadyExistsException;
use App\Exceptions\NotFoundException;
use App\Models\SchoolUnit;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use App\XmlExports\Register\StudentsRegisterXmlExport;
use Illuminate\Http\Request;

class StudentRegistryController extends Controller
{
	public function lookup(SchoolUnit $schoolUnit)
	{
		$studentRegistry = $schoolUnit->studentRegistry;
		if (!$studentRegistry) throw new NotFoundException("STUDENT_REGISTRY");
		return response()->json([ "registryId" => $studentRegistry->id ]);
	}

	public function list()
	{
		return StudentRegistry::all()->toResourceCollection();
	}

	public function create(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"schoolUnitId" => "required|integer|exists:school_units,id"
		]);
		$schoolUnitId = $validator["schoolUnitId"];
		if (StudentRegistry::where("school_unit_id", $schoolUnitId)->exists()) {
			throw new EntityAlreadyExistsException("STUDENT_REGISTRY");
		}

		$registry = new StudentRegistry();
		$registry->school_unit_id = $schoolUnitId;
		$registry->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function export(Request $request, StudentRegistry $studentRegistry) {
		$xmlExport = new StudentsRegisterXmlExport(
			SchoolUnit::where("id", "=", $studentRegistry->school_unit_id)->first(),
			$studentRegistry->created_at, $studentRegistry
		);
		return $request->input("format") == "xml" ? $xmlExport->downloadXml() : $xmlExport->downloadHtml();
	}
}
