<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facility\CreateFacilityRequest;
use App\Http\Requests\Facility\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FacilityController extends Controller
{
    use ApiResponse;

    /**
     * List all facilities.
     */
    public function index(): JsonResponse
    {
        $facilities = Facility::with(['doctors', 'blogs'])->paginate(10);
        $resourceCollection = FacilityResource::collection($facilities);

        return $this->paginatedResponse('Facilities retrieved successfully', $facilities, $resourceCollection);
    }

    /**
     * Show a specific facility.
     */
    public function show(Facility $facility): JsonResponse
    {
        $facility->load(['doctors', 'blogs']);

        return $this->successResponse('Facility retrieved successfully', new FacilityResource($facility));
    }

    /**
     * Create a new facility.
     */
    public function store(CreateFacilityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated);

        // Accordions should be in validated data, but ensure it's properly formatted
        if (isset($data['accordions']) && is_array($data['accordions']) && ! empty($data['accordions'])) {
            // Filter out any invalid entries
            $accordions = array_filter($data['accordions'], function ($accordion) {
                return isset($accordion) && is_array($accordion) && (! empty($accordion['title']) || ! empty($accordion['description']));
            });
            $data['accordions'] = ! empty($accordions) ? array_values($accordions) : null;
        } else {
            // If not in validated or empty, check raw input as fallback
            $rawAccordions = $request->input('accordions');
            if (! empty($rawAccordions) && is_array($rawAccordions)) {
                $accordions = array_filter($rawAccordions, function ($accordion) {
                    return isset($accordion) && is_array($accordion) && (! empty($accordion['title']) || ! empty($accordion['description']));
                });
                $data['accordions'] = ! empty($accordions) ? array_values($accordions) : null;
            } else {
                $data['accordions'] = null;
            }
        }

        $facility = Facility::create($data);

        return $this->successResponse('Facility created successfully', new FacilityResource($facility), 201);
    }

    /**
     * Update a facility.
     */
    public function update(UpdateFacilityRequest $request, Facility $facility): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated, $facility);

        $updateData = [];
        foreach ($facility->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        // Handle accordions from request input (form data like accordions[0][title])
        if ($request->has('accordions')) {
            $accordions = $request->input('accordions');
            if (is_array($accordions)) {
                // Filter out empty accordion entries
                $accordions = array_filter($accordions, function ($accordion) {
                    return ! empty($accordion['title']) || ! empty($accordion['description']);
                });
                $updateData['accordions'] = array_values($accordions); // Re-index array
            } else {
                $updateData['accordions'] = null;
            }
        }

        if (! empty($updateData)) {
            $facility->update($updateData);
        }

        return $this->successResponse('Facility updated successfully', new FacilityResource($facility));
    }

    /**
     * Delete a facility.
     */
    public function destroy(Facility $facility): JsonResponse
    {
        if ($facility->image && Storage::disk('public')->exists($facility->image)) {
            Storage::disk('public')->delete($facility->image);
        }

        $facility->delete();

        return $this->successResponse('Facility deleted successfully');
    }

    /**
     * Toggle facility status between active and inactive.
     */
    public function setActive(Facility $facility): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $facility->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $facility->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Facility set as active successfully'
            : 'Facility set as inactive successfully';

        return $this->successResponse($statusMessage, new FacilityResource($facility->fresh()));
    }

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateFacilityRequest|UpdateFacilityRequest $request, array $data, ?Facility $facility = null): array
    {
        if ($request->hasFile('image')) {
            if ($facility !== null && $facility->image) {
                $oldImage = $facility->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('facilities', 'public');
            $data['image'] = $path;
        }

        return $data;
    }
}
