<?php

namespace App\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Models\SchoolUnit;
use App\Utilities\ValidatorAssistant\ValidatorAssistant;
use App\XmlExports\Register\ChildrenRegisterXmlExport;
use Illuminate\Http\Request;

class ChildrenRegistryController extends Controller
{
	public function list()
	{
		return ChildrenRegistry::all()->toResourceCollection();
	}

	public function create(Request $request)
	{
		$validator = ValidatorAssistant::validate($request, [
			"schoolUnitId" => "required|integer|exists:school_units,id"
		]);
		$schoolUnitId = $validator["schoolUnitId"];
		if (ChildrenRegistry::where("school_unit_id", $schoolUnitId)->exists()) {
			return \Response::json([
				"success" => false,
				"errors" => [
					"CHILDREN_REGISTRY_ALREADY_EXISTS"
				]
			], 409);
		}

		$registry = new ChildrenRegistry();
		$registry->school_unit_id = $schoolUnitId;
		$registry->save();

		return \Response::json([
			"success" => true
		], 201);
	}

	public function export(Request $request, ChildrenRegistry $childrenRegistry) {
		$xmlExport = new ChildrenRegisterXmlExport(
			SchoolUnit::where("id", "=", $childrenRegistry->school_unit_id)->first(),
			$childrenRegistry->created_at, $childrenRegistry
		);
		return $request->input("format") == "xml" ? $xmlExport->downloadXml() : $xmlExport->downloadHtml();
	}
}
