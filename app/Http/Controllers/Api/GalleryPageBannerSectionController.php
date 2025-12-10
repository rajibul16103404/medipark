<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryPageBannerSection\CreateGalleryPageBannerSectionRequest;
use App\Http\Resources\GalleryPageBannerSectionResource;
use App\Models\GalleryPageBannerSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GalleryPageBannerSectionController extends Controller
{
    use ApiResponse;

    /**
     * List the gallery page banner section (singleton - only one record exists).
     */
    public function index(): JsonResponse
    {
        $bannerSection = GalleryPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('Gallery page banner section not found', 404);
        }

        return $this->successResponse('Gallery page banner section retrieved successfully', new GalleryPageBannerSectionResource($bannerSection));
    }

    /**
     * Get the gallery page banner section (singleton - only one record exists).
     */
    public function show(): JsonResponse
    {
        $bannerSection = GalleryPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('Gallery page banner section not found', 404);
        }

        return $this->successResponse('Gallery page banner section retrieved successfully', new GalleryPageBannerSectionResource($bannerSection));
    }

    /**
     * Create or update the single gallery page banner section (singleton).
     */
    public function store(CreateGalleryPageBannerSectionRequest $request): JsonResponse
    {
        $bannerSection = GalleryPageBannerSection::first();

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

            return $this->successResponse('Gallery page banner section updated successfully', new GalleryPageBannerSectionResource($bannerSection->fresh()));
        }

        $bannerSection = GalleryPageBannerSection::create($data);

        return $this->successResponse('Gallery page banner section created successfully', new GalleryPageBannerSectionResource($bannerSection), 201);
    }

    /**
     * Toggle gallery page banner section status between active and inactive.
     */
    public function setActive(): JsonResponse
    {
        $bannerSection = GalleryPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('Gallery page banner section not found', 404);
        }

        $newStatus = $bannerSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $bannerSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Gallery page banner section set as active successfully'
            : 'Gallery page banner section set as inactive successfully';

        return $this->successResponse($statusMessage, new GalleryPageBannerSectionResource($bannerSection->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateGalleryPageBannerSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request): array
    {
        $requestData = $request->all();
        $bannerSection = GalleryPageBannerSection::first();

        // Handle background_image upload
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = $file->store('gallery-page-banner-sections', 'public');
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
