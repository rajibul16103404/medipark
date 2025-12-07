<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPageBannerSection\CreateAboutUsPageBannerSectionRequest;
use App\Http\Requests\AboutUsPageBannerSection\UpdateAboutUsPageBannerSectionRequest;
use App\Http\Resources\AboutUsPageBannerSectionResource;
use App\Models\AboutUsPageBannerSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsPageBannerSectionController extends Controller
{
    use ApiResponse;

    /**
     * Get the single about us page banner section.
     */
    public function index(): JsonResponse
    {
        $bannerSection = AboutUsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('No about us page banner section found. You can create one using POST /api/about-us-page-banner-sections', 404);
        }

        return $this->successResponse('About us page banner section retrieved successfully', new AboutUsPageBannerSectionResource($bannerSection));
    }

    /**
     * Show the about us page banner section (if active).
     */
    public function show(): JsonResponse
    {
        $bannerSection = AboutUsPageBannerSection::active();

        if (! $bannerSection) {
            return $this->errorResponse('No active about us page banner section found', 404);
        }

        return $this->successResponse('About us page banner section retrieved successfully', new AboutUsPageBannerSectionResource($bannerSection));
    }

    /**
     * Show a specific about us page banner section.
     */
    public function showById(AboutUsPageBannerSection $aboutUsPageBannerSection): JsonResponse
    {
        return $this->successResponse('About us page banner section retrieved successfully', new AboutUsPageBannerSectionResource($aboutUsPageBannerSection));
    }

    /**
     * Create or update the single about us page banner section.
     */
    public function store(CreateAboutUsPageBannerSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $bannerSection = AboutUsPageBannerSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $bannerSection);

        if ($bannerSection) {
            // Update existing record
            $bannerSection->update($data);

            return $this->successResponse('About us page banner section updated successfully', new AboutUsPageBannerSectionResource($bannerSection->fresh()));
        }

        // Create new record
        $bannerSection = AboutUsPageBannerSection::create($data);

        return $this->successResponse('About us page banner section created successfully', new AboutUsPageBannerSectionResource($bannerSection), 201);
    }

    /**
     * Update a about us page banner section by ID.
     */
    public function update(UpdateAboutUsPageBannerSectionRequest $request, AboutUsPageBannerSection $aboutUsPageBannerSection): JsonResponse
    {
        // Get all fillable fields from request - check each field individually for form data
        $data = [];
        $fillableFields = ['opacity', 'status'];
        $requestData = $request->all();

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle background_image (checks hasFile separately)
        $data = $this->processFileUploads($data, $request, $aboutUsPageBannerSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $aboutUsPageBannerSection->update($data);
        }

        return $this->successResponse('About us page banner section updated successfully', new AboutUsPageBannerSectionResource($aboutUsPageBannerSection->fresh()));
    }

    /**
     * Delete a about us page banner section.
     */
    public function destroy(AboutUsPageBannerSection $aboutUsPageBannerSection): JsonResponse
    {
        $aboutUsPageBannerSection->delete();

        return $this->successResponse('About us page banner section deleted successfully');
    }

    /**
     * Toggle about us page banner section status between active and inactive.
     */
    public function setActive(AboutUsPageBannerSection $aboutUsPageBannerSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $aboutUsPageBannerSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $aboutUsPageBannerSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page banner section set as active successfully'
            : 'About us page banner section set as inactive successfully';

        return $this->successResponse($statusMessage, new AboutUsPageBannerSectionResource($aboutUsPageBannerSection->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPageBannerSectionRequest|UpdateAboutUsPageBannerSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?AboutUsPageBannerSection $bannerSection = null): array
    {
        $requestData = $request->all();

        // Handle background_image upload
        if ($request->hasFile('background_image')) {
            // Delete old file if updating
            if ($bannerSection && $bannerSection->background_image) {
                $oldPath = str_replace('/storage/', '', $bannerSection->background_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('background_image');
            $path = $file->store('about-us-page-banner-sections', 'public');
            $data['background_image'] = '/storage/'.$path;
        } elseif (array_key_exists('background_image', $requestData) && is_string($request->input('background_image'))) {
            // If background_image is provided as a string URL in form data, use it
            $data['background_image'] = $request->input('background_image');
        } elseif (! array_key_exists('background_image', $requestData) && ! $request->hasFile('background_image') && $bannerSection) {
            // Only preserve existing background_image if not provided at all
            $data['background_image'] = $bannerSection->background_image;
        }

        return $data;
    }
}
