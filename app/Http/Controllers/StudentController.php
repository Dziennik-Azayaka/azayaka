<?php

namespace App\Http\Controllers;

use App\Models\ChildrenRegistry;
use App\Models\ResidenceAddress;
use App\Models\Student;
use App\Models\StudentRegistry;
use App\Utilities\ValidatorAssistant;
use Illuminate\Http\Request;

class StudentController extends Controller
{
	public function create(Request $request, StudentRegistry $studentRegistry)
	{
		$validator = ValidatorAssistant::validate($request, [
			"firstName" => "required|max:255",
			"lastName" => "required|max:255",
			"secondName" => "nullable|max:255",
			"pesel" => "required|size:11|unique:students,pesel",
			"alternateIdentityDocument" => "nullable|max:255|unique:students,alternate_identity_document",
			"birthdate" => "required|date",
			"birthplace" => "required|max:255",
			"gender" => "required|in:male,female",
			"admissionDate" => "required|date",
			"residenceAddressCountry" => "required|max:255",
			"residenceAddressCommune" => "nullable|max:255",
			"residenceAddressTown" => "nullable|max:255",
			"residenceAddressPostalCode" => "nullable|max:255",
			"residenceAddressHouseNumber" => "nullable|max:255",
			"residenceAddressStreet" => "nullable|max:255",
			"childrenRegistryId" => "nullable|exists:children_registries,id"
		]);

		if (!$validator["success"]) {
			return $validator["errorResponse"];
		}

		$validated = $validator["data"];

		$residenceAddress = new ResidenceAddress();
		$residenceAddress->country = $validated["residenceAddressCountry"];
		$residenceAddress->commune = $validated["residenceAddressCommune"];
		$residenceAddress->town = $validated["residenceAddressTown"];
		$residenceAddress->postal_code = $validated["residenceAddressPostalCode"];
		$residenceAddress->house_number = $validated["residenceAddressHouseNumber"];
		$residenceAddress->street = $validated["residenceAddressStreet"];
		$residenceAddress->save();

		$student = new Student();
		$student->first_name = $validated["firstName"];
		$student->last_name = $validated["lastName"];
		$student->second_name = $validated["secondName"];
		$student->pesel = $validated["pesel"];
		$student->alternate_identity_document = $validated["alternateIdentityDocument"];
		$student->birthdate = $validated["birthdate"];
		$student->birthplace = $validated["birthplace"];
		$student->gender = $validated["gender"];
		$student->admission_date = $validated["admissionDate"];
		$student->residence_address_id = $residenceAddress->id;

		$studentRegistry->students()->attach($student);

		if ($validated["childrenRegistryId"] != null) {
			ChildrenRegistry::find($validated["childrenRegistryId"])->students()->attach($student);
		}

		return \Response::json([
			"success" => true
		], 201);
    }
}
