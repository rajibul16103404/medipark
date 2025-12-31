<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FutureVenture\CreateFutureVentureRequest;
use App\Http\Requests\FutureVenture\UpdateFutureVentureRequest;
use App\Http\Resources\FutureVentureResource;
use App\Models\FutureVenture;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FutureVentureController extends Controller
{
    use ApiResponse;

    /**
     * List all future ventures.
     */
    public function index(): JsonResponse
    {
        $futureVentures = FutureVenture::paginate(10);
        $resourceCollection = FutureVentureResource::collection($futureVentures);

        return $this->paginatedResponse('Future ventures retrieved successfully', $futureVentures, $resourceCollection);
    }

    /**
     * Show a specific future venture.
     */
    public function show(FutureVenture $futureVenture): JsonResponse
    {
        return $this->successResponse('Future venture retrieved successfully', new FutureVentureResource($futureVenture));
    }

    /**
     * Create a new future venture.
     */
    public function store(CreateFutureVentureRequest $request): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated());

        $futureVenture = FutureVenture::create($data);

        return $this->successResponse('Future venture created successfully', new FutureVentureResource($futureVenture), 201);
    }

    /**
     * Update a future venture.
     */
    public function update(UpdateFutureVentureRequest $request, FutureVenture $futureVenture): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated(), $futureVenture);

        $updateData = [];
        foreach ($futureVenture->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $futureVenture->update($updateData);
        }

        return $this->successResponse('Future venture updated successfully', new FutureVentureResource($futureVenture->fresh()));
    }

    /**
     * Delete a future venture.
     */
    public function destroy(FutureVenture $futureVenture): JsonResponse
    {
        if ($futureVenture->image && Storage::disk('public')->exists($futureVenture->image)) {
            Storage::disk('public')->delete($futureVenture->image);
        }

        $futureVenture->delete();

        return $this->successResponse('Future venture deleted successfully');
    }

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateFutureVentureRequest|UpdateFutureVentureRequest $request, array $data, ?FutureVenture $futureVenture = null): array
    {
        if ($request->hasFile('image')) {
            if ($futureVenture !== null && $futureVenture->image) {
                $oldImage = $futureVenture->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('future-ventures', 'public');
            $data['image'] = $path;
        }

        return $data;
    }
}
