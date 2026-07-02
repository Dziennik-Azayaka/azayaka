<?php

namespace App\Http\Controllers;

use App\Enums\AttendancePrimitiveType;
use App\Http\Requests\AttendanceComplexTypeRequest;
use App\Models\AttendanceComplexType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class AttendanceComplexTypeController extends Controller
{
	public function list()
	{
		return AttendanceComplexType::all()->map(function (AttendanceComplexType $type) {
			return [
				"id" => $type->id,
				"name" => $type->name,
				"shortcut" => $type->shortcut,
				"active" => $type->active,
				"primitiveType" => $type->maps_to_primitive_type
			];
		});
	}

	public function create(AttendanceComplexTypeRequest $request)
	{
		$validated = $request->validated();
		$type = new AttendanceComplexType();
		$type->name = $validated["name"];
		$type->shortcut = $validated["shortcut"];
		$type->maps_to_primitive_type = $validated["mapsToPrimitiveType"];
		$type->save();

		return \Response::json([
			"success" => true,
			"id" => $type->id
		], 201);
	}

	public function update(AttendanceComplexTypeRequest $request, AttendanceComplexType $type)
	{
		if ($type->built_in) {
			return response()->json([
				"success" => false,
				"errors" => ["CANNOT_EDIT_BUILT_IN_TYPE"]
			], 409);
		}

		$validated = $request->validated();
		$type->update([
			"name" => $validated["name"],
			"shortcut" => $validated["shortcut"],
			"maps_to_primitive_type" => $validated["mapsToPrimitiveType"]
		]);

		return [
			"success" => true
		];
	}

	public function changeActivity(AttendanceComplexType $type)
	{
		$type->active = !$type->active;
		$type->save();

		return [
			"success" => true
		];
	}
}
