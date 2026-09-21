<?php

namespace App\Utilities;

use App\Documents\AccountAccessesActivation\AccountAccessesActivationDocument;
use App\Enums\AccessType;
use App\Models\AccountAccess;
use App\Models\Employee;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AccountAccessDocumentGenerator
{
	protected AccessType $accessType;

	protected array $ids;

	public function __construct(AccessType $accessType, array $ids)
	{
		$this->accessType = $accessType;
		$this->ids = array_unique($ids);
	}

	public function generateDocument(): Response|JsonResponse
	{
		$model = match ($this->accessType) {
			AccessType::STUDENT => Student::class,
			AccessType::PARENT => Guardian::class,
			AccessType::EMPLOYEE => Employee::class
		};

		$idKey = match ($this->accessType) {
			AccessType::STUDENT => "student_id",
			AccessType::PARENT => "guardian_id",
			AccessType::EMPLOYEE => "employee_id"
		};

		$entities = $model::whereIn("id", $this->ids)->get();
		$entityIds = $entities->pluck("id");
		$accesses = AccountAccess::whereIn($idKey, $entityIds)->get();

		$document = new AccountAccessesActivationDocument;

		foreach ($entities as $entity) {
			$access = $accesses->where($idKey, $entity->id)->first();
			if ($access?->words == null) {
				return response()->json([
					"success" => false,
					"errors" => [
						"ENTITY_HAS_NO_ACCESS_WORDS",
					],
				], 422);
			}

			$firstName = $this->accessType == AccessType::EMPLOYEE ? $entity->first_name : $entity->person->first_name;
			$lastName = $this->accessType == AccessType::EMPLOYEE ? $entity->last_name : $entity->person->last_name;

			$document->addAccess($this->accessType, $firstName . " " . $lastName,
				explode(",", $access->words));
		}

		$document->generateDocument();

		return $document->streamDocument();
	}
}
