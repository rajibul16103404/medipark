<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactPageBannerSection\CreateContactPageBannerSectionRequest;
use App\Http\Resources\ContactPageBannerSectionResource;
use App\Models\ContactPageBannerSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ContactPageBannerSectionController extends Controller
{
    use ApiResponse;

    /**
     * List contact page banner section (singleton - only one record exists).
     */
    public function index(): JsonResponse
    {
        $bannerSection = ContactPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('Contact page banner section not found', 404);
        }

        return $this->successResponse('Contact page banner section retrieved successfully', new ContactPageBannerSectionResource($bannerSection));
    }

    /**
     * Get the contact page banner section (singleton - only one record exists).
     */
    public function show(): JsonResponse
    {
        $bannerSection = ContactPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('Contact page banner section not found', 404);
        }

        return $this->successResponse('Contact page banner section retrieved successfully', new ContactPageBannerSectionResource($bannerSection));
    }

    /**
     * Create or update contact page banner section (singleton - only one record allowed).
     */
    public function store(CreateContactPageBannerSectionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Process file uploads
        $data = $this->processFileUploads($validated, $request);

        // Get the existing banner section or create a new one
        $bannerSection = ContactPageBannerSection::first();

        if ($bannerSection) {
            // Update existing record
            // Delete old background image if new one is being uploaded
            if ($request->hasFile('background_image') && $bannerSection->background_image) {
                $oldPath = str_replace('/storage/', '', $bannerSection->background_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $bannerSection->update($data);
            $message = 'Contact page banner section updated successfully';
            $statusCode = 200;
        } else {
            // Create new record
            $bannerSection = ContactPageBannerSection::create($data);
            $message = 'Contact page banner section created successfully';
            $statusCode = 201;
        }

        return $this->successResponse($message, new ContactPageBannerSectionResource($bannerSection->fresh()), $statusCode);
    }

    /**
     * Toggle contact page banner section status between active and inactive.
     */
    public function setActive(): JsonResponse
    {
        $bannerSection = ContactPageBannerSection::first();

        if (! $bannerSection) {
            return $this->errorResponse('Contact page banner section not found', 404);
        }

        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $bannerSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $bannerSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Contact page banner section set as active successfully'
            : 'Contact page banner section set as inactive successfully';

        return $this->successResponse($statusMessage, new ContactPageBannerSectionResource($bannerSection->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateContactPageBannerSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request): array
    {
        $requestData = $request->all();
        $bannerSection = ContactPageBannerSection::first();

        // Handle background_image upload
        if ($request->hasFile('background_image')) {
            $file = $request->file('background_image');
            $path = $file->store('contact-page-banner-sections', 'public');
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
