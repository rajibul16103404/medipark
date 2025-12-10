<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepageCtaSection\CreateHomepageCtaSectionRequest;
use App\Http\Requests\HomepageCtaSection\UpdateHomepageCtaSectionRequest;
use App\Http\Resources\HomepageCtaSectionResource;
use App\Models\HomepageCtaSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class HomepageCtaSectionController extends Controller
{
    use ApiResponse;

    /**
     * Get the single homepage CTA section.
     */
    public function index(): JsonResponse
    {
        $ctaSection = HomepageCtaSection::first();

        if (! $ctaSection) {
            return $this->errorResponse('No homepage CTA section found. You can create one using POST /api/homepage-cta-sections', 404);
        }

        return $this->successResponse('Homepage CTA section retrieved successfully', new HomepageCtaSectionResource($ctaSection));
    }

    /**
     * Show the homepage CTA section (if active).
     */
    public function show(): JsonResponse
    {
        $ctaSection = HomepageCtaSection::active();

        if (! $ctaSection) {
            return $this->errorResponse('No active homepage CTA section found', 404);
        }

        return $this->successResponse('Homepage CTA section retrieved successfully', new HomepageCtaSectionResource($ctaSection));
    }

    /**
     * Show a specific homepage CTA section.
     */
    public function showById(HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        return $this->successResponse('Homepage CTA section retrieved successfully', new HomepageCtaSectionResource($homepageCtaSection));
    }

    /**
     * Create or update the single homepage CTA section.
     */
    public function store(CreateHomepageCtaSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $ctaSection = HomepageCtaSection::first();

        $data = $request->validated();

        if ($ctaSection) {
            // Update existing record
            $ctaSection->update($data);

            return $this->successResponse('Homepage CTA section updated successfully', new HomepageCtaSectionResource($ctaSection->fresh()));
        }

        // Create new record
        $ctaSection = HomepageCtaSection::create($data);

        return $this->successResponse('Homepage CTA section created successfully', new HomepageCtaSectionResource($ctaSection), 201);
    }

    /**
     * Update a homepage CTA section by ID.
     */
    public function update(UpdateHomepageCtaSectionRequest $request, HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        $validated = $request->validated();

        // Only update if we have data to update
        if (! empty($validated)) {
            $homepageCtaSection->update($validated);
        }

        return $this->successResponse('Homepage CTA section updated successfully', new HomepageCtaSectionResource($homepageCtaSection->fresh()));
    }

    /**
     * Delete a homepage CTA section.
     */
    public function destroy(HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        $homepageCtaSection->delete();

        return $this->successResponse('Homepage CTA section deleted successfully');
    }

    /**
     * Toggle homepage CTA section status between active and inactive.
     */
    public function setActive(HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $homepageCtaSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $homepageCtaSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Homepage CTA section set as active successfully'
            : 'Homepage CTA section set as inactive successfully';

        return $this->successResponse($statusMessage, new HomepageCtaSectionResource($homepageCtaSection->fresh()));
    }
}
