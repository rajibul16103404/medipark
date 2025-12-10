<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewsPageBannerSection\CreateNewsPageBannerSectionRequest;
use App\Http\Resources\NewsPageBannerSectionResource;
use App\Models\NewsPageBannerSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class NewsPageBannerSectionController extends Controller
{
    use ApiResponse;

    /**
     * List the news page banner section (singleton - only one record exists).
     */
    public function index(): JsonResponse
    {
        $bannerSection = NewsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('News page banner section not found', 404);
        }

        return $this->successResponse('News page banner section retrieved successfully', new NewsPageBannerSectionResource($bannerSection));
    }

    /**
     * Get the news page banner section (singleton - only one record exists).
     */
    public function show(): JsonResponse
    {
        $bannerSection = NewsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('News page banner section not found', 404);
        }

        return $this->successResponse('News page banner section retrieved successfully', new NewsPageBannerSectionResource($bannerSection));
    }

    /**
     * Create or update the single news page banner section (singleton).
     */
    public function store(CreateNewsPageBannerSectionRequest $request): JsonResponse
    {
        $bannerSection = NewsPageBannerSection::first();

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

            return $this->successResponse('News page banner section updated successfully', new NewsPageBannerSectionResource($bannerSection->fresh()));
        }

        $bannerSection = NewsPageBannerSection::create($data);

        return $this->successResponse('News page banner section created successfully', new NewsPageBannerSectionResource($bannerSection), 201);
    }

    /**
     * Toggle news page banner section status between active and inactive.
     */
    public function setActive(): JsonResponse
    {
        $bannerSection = NewsPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('News page banner section not found', 404);
        }

        $newStatus = $bannerSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $bannerSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'News page banner section set as active successfully'
            : 'News page banner section set as inactive successfully';

        return $this->successResponse($statusMessage, new NewsPageBannerSectionResource($bannerSection->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateNewsPageBannerSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request): array
    {
        $requestData = $request->all();
        $bannerSection = NewsPageBannerSection::first();

        // Handle background_image upload
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = $file->store('news-page-banner-sections', 'public');
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
