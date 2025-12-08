<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\CreateInvestorRequest;
use App\Http\Requests\Investor\UpdateInvestorRequest;
use App\Http\Resources\InvestorResource;
use App\Models\Investor;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class InvestorController extends Controller
{
    use ApiResponse;

    /**
     * List all investors.
     */
    public function index(): JsonResponse
    {
        $investors = Investor::latest()->paginate(10);
        $resourceCollection = InvestorResource::collection($investors);

        return $this->paginatedResponse('Investors retrieved successfully', $investors, $resourceCollection);
    }

    /**
     * Store a new investor.
     */
    public function store(CreateInvestorRequest $request): JsonResponse
    {
        $data = $request->validated();

        $data = $this->processFileUploads($request, $data);

        $investor = Investor::create($data);

        return $this->successResponse('Investor created successfully', new InvestorResource($investor), 201);
    }

    /**
     * Show an investor.
     */
    public function show(Investor $investor): JsonResponse
    {
        return $this->successResponse('Investor retrieved successfully', new InvestorResource($investor));
    }

    /**
     * Update an investor.
     */
    public function update(UpdateInvestorRequest $request, Investor $investor): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated);

        // Only update fillable fields
        $updateData = [];
        foreach ($investor->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $investor->update($updateData);
        }

        return $this->successResponse('Investor updated successfully', new InvestorResource($investor));
    }

    /**
     * Delete an investor.
     */
    public function destroy(Investor $investor): JsonResponse
    {
        $investor->delete();

        return $this->successResponse('Investor deleted successfully');
    }

    /**
     * Handle applicant and nominee image uploads.
     */
    private function processFileUploads(CreateInvestorRequest|UpdateInvestorRequest $request, array $data): array
    {
        foreach (['applicant_image', 'nominee_image'] as $field) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store('investors', 'public');
                $data[$field] = $path;
            }
        }

        return $data;
    }
}
