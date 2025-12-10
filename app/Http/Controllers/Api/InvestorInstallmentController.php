<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InvestorInstallment\CreateInvestorInstallmentRequest;
use App\Http\Requests\InvestorInstallment\UpdateInvestorInstallmentRequest;
use App\Http\Resources\InvestorInstallmentResource;
use App\Models\InvestorInstallment;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class InvestorInstallmentController extends Controller
{
    use ApiResponse;

    /**
     * List all investor installments.
     */
    public function index(): JsonResponse
    {
        $installments = InvestorInstallment::with('investor')
            ->latest()
            ->paginate(10);
        $resourceCollection = InvestorInstallmentResource::collection($installments);

        return $this->paginatedResponse('Investor installments retrieved successfully', $installments, $resourceCollection);
    }

    /**
     * Store a new investor installment.
     */
    public function store(CreateInvestorInstallmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $installment = InvestorInstallment::create($data);

        return $this->successResponse('Investor installment created successfully', new InvestorInstallmentResource($installment->load('investor')), 201);
    }

    /**
     * Show an investor installment.
     */
    public function show(InvestorInstallment $investorInstallment): JsonResponse
    {
        return $this->successResponse('Investor installment retrieved successfully', new InvestorInstallmentResource($investorInstallment->load('investor')));
    }

    /**
     * Update an investor installment.
     */
    public function update(UpdateInvestorInstallmentRequest $request, InvestorInstallment $investorInstallment): JsonResponse
    {
        $validated = $request->validated();

        // Only update fillable fields
        $updateData = [];
        foreach ($investorInstallment->getFillable() as $field) {
            if (array_key_exists($field, $validated)) {
                $updateData[$field] = $validated[$field];
            }
        }

        if (! empty($updateData)) {
            $investorInstallment->update($updateData);
        }

        return $this->successResponse('Investor installment updated successfully', new InvestorInstallmentResource($investorInstallment->fresh()->load('investor')));
    }

    /**
     * Delete an investor installment.
     */
    public function destroy(InvestorInstallment $investorInstallment): JsonResponse
    {
        $investorInstallment->delete();

        return $this->successResponse('Investor installment deleted successfully');
    }
}
