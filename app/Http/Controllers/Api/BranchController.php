<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Branch\CreateBranchRequest;
use App\Http\Requests\Branch\UpdateBranchRequest;
use App\Http\Resources\BranchResource;
use App\Models\Branch;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    use ApiResponse;

    /**
     * List all branches.
     */
    public function index(): JsonResponse
    {
        $branches = Branch::paginate(10);
        $resourceCollection = BranchResource::collection($branches);

        return $this->paginatedResponse('Branches retrieved successfully', $branches, $resourceCollection);
    }

    /**
     * Show a specific branch.
     */
    public function show(Branch $branch): JsonResponse
    {
        return $this->successResponse('Branch retrieved successfully', new BranchResource($branch));
    }

    /**
     * Create a new branch.
     */
    public function store(CreateBranchRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return DB::transaction(function () use ($validated) {
            // If setting this branch as main, remove main status from all other branches
            if (! empty($validated['is_main']) && $validated['is_main']) {
                Branch::where('is_main', true)->update(['is_main' => false]);
            }

            $branch = Branch::create($validated);

            return $this->successResponse('Branch created successfully', new BranchResource($branch), 201);
        });
    }

    /**
     * Update a branch.
     */
    public function update(UpdateBranchRequest $request, Branch $branch): JsonResponse
    {
        $updateData = [];
        foreach ($branch->getFillable() as $field) {
            if (array_key_exists($field, $request->validated())) {
                $updateData[$field] = $request->validated()[$field];
            }
        }

        return DB::transaction(function () use ($updateData, $branch) {
            // If setting this branch as main, remove main status from all other branches
            if (isset($updateData['is_main']) && $updateData['is_main']) {
                Branch::where('id', '!=', $branch->id)
                    ->where('is_main', true)
                    ->update(['is_main' => false]);
            }

            if (! empty($updateData)) {
                $branch->update($updateData);
            }

            return $this->successResponse('Branch updated successfully', new BranchResource($branch->fresh()));
        });
    }

    /**
     * Delete a branch.
     */
    public function destroy(Branch $branch): JsonResponse
    {
        $branch->delete();

        return $this->successResponse('Branch deleted successfully');
    }

    /**
     * Toggle branch status between active and inactive.
     */
    public function setActive(Branch $branch): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $branch->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $branch->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Branch set as active successfully'
            : 'Branch set as inactive successfully';

        return $this->successResponse($statusMessage, new BranchResource($branch->fresh()));
    }
}
