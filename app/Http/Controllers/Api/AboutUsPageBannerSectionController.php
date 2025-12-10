<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPageBannerSection\CreateAboutUsPageBannerSectionRequest;
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
     * List the about us page banner section (singleton - only one record exists).
     */
    public function index(): JsonResponse
    {
        $bannerSection = AboutUsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('About us page banner section not found', 404);
        }

        return $this->successResponse('About us page banner section retrieved successfully', new AboutUsPageBannerSectionResource($bannerSection));
    }

    /**
     * Get the about us page banner section (singleton - only one record exists).
     */
    public function show(): JsonResponse
    {
        $bannerSection = AboutUsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('About us page banner section not found', 404);
        }

        return $this->successResponse('About us page banner section retrieved successfully', new AboutUsPageBannerSectionResource($bannerSection));
    }

    /**
     * Create or update the single about us page banner section (singleton).
     */
    public function store(CreateAboutUsPageBannerSectionRequest $request): JsonResponse
    {
        $bannerSection = AboutUsPageBannerSection::first();

        $data = $this->processFileUploads($request->validated(), $request);

        if ($bannerSection) {
            // Delete old background image if a new one is uploaded
            if ($request->hasFile('background_image') && $bannerSection->background_image) {
                $oldPath = str_replace('/storage/', '', $bannerSection->background_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $bannerSection->update($data);

            return $this->successResponse('About us page banner section updated successfully', new AboutUsPageBannerSectionResource($bannerSection->fresh()));
        }

        $bannerSection = AboutUsPageBannerSection::create($data);

        return $this->successResponse('About us page banner section created successfully', new AboutUsPageBannerSectionResource($bannerSection), 201);
    }

    /**
     * Toggle about us page banner section status between active and inactive.
     */
    public function setActive(): JsonResponse
    {
        $bannerSection = AboutUsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('About us page banner section not found', 404);
        }

        $newStatus = $bannerSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $bannerSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page banner section set as active successfully'
            : 'About us page banner section set as inactive successfully';

        return $this->successResponse($statusMessage, new AboutUsPageBannerSectionResource($bannerSection->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPageBannerSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request): array
    {
        $requestData = $request->all();
        $bannerSection = AboutUsPageBannerSection::first();

        // Handle background_image upload
        if ($request->hasFile('background_image')) {
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
