<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InstallmentRule\CreateInstallmentRuleRequest;
use App\Http\Requests\InstallmentRule\UpdateInstallmentRuleRequest;
use App\Http\Resources\InstallmentRuleResource;
use App\Models\InstallmentRule;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class InstallmentRuleController extends Controller
{
    use ApiResponse;

    /**
     * List all installment rules.
     */
    public function index(): JsonResponse
    {
        $installmentRules = InstallmentRule::paginate(10);
        $resourceCollection = InstallmentRuleResource::collection($installmentRules);

        return $this->paginatedResponse('Installment rules retrieved successfully', $installmentRules, $resourceCollection);
    }

    /**
     * Show a specific installment rule.
     */
    public function show(InstallmentRule $installmentRule): JsonResponse
    {
        return $this->successResponse('Installment rule retrieved successfully', new InstallmentRuleResource($installmentRule));
    }

    /**
     * Create a new installment rule.
     */
    public function store(CreateInstallmentRuleRequest $request): JsonResponse
    {
        $installmentRule = InstallmentRule::create($request->validated());

        return $this->successResponse('Installment rule created successfully', new InstallmentRuleResource($installmentRule), 201);
    }

    /**
     * Update an installment rule.
     */
    public function update(UpdateInstallmentRuleRequest $request, InstallmentRule $installmentRule): JsonResponse
    {
        $updateData = [];
        foreach ($installmentRule->getFillable() as $field) {
            if (array_key_exists($field, $request->validated())) {
                $updateData[$field] = $request->validated()[$field];
            }
        }

        if (! empty($updateData)) {
            $installmentRule->update($updateData);
        }

        return $this->successResponse('Installment rule updated successfully', new InstallmentRuleResource($installmentRule));
    }

    /**
     * Delete an installment rule.
     */
    public function destroy(InstallmentRule $installmentRule): JsonResponse
    {
        $installmentRule->delete();

        return $this->successResponse('Installment rule deleted successfully');
    }

    /**
     * Toggle installment rule status between active and inactive.
     */
    public function setActive(InstallmentRule $installmentRule): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $installmentRule->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $installmentRule->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Installment rule set as active successfully'
            : 'Installment rule set as inactive successfully';

        return $this->successResponse($statusMessage, new InstallmentRuleResource($installmentRule->fresh()));
    }
}
