<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPageAfterOurVisionSection\CreateAboutUsPageAfterOurVisionSectionRequest;
use App\Http\Requests\AboutUsPageAfterOurVisionSection\UpdateAboutUsPageAfterOurVisionSectionRequest;
use App\Http\Resources\AboutUsPageAfterOurVisionSectionResource;
use App\Models\AboutUsPageAfterOurVisionSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsPageAfterOurVisionSectionController extends Controller
{
    use ApiResponse;

    /**
     * Get the single about us page after our vision section.
     */
    public function index(): JsonResponse
    {
        $afterOurVisionSection = AboutUsPageAfterOurVisionSection::first();

        if (! $afterOurVisionSection) {
            return response()->json([
                'message' => 'No about us page after our vision section found. You can create one using POST /api/about-us-page-after-our-vision-sections',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new AboutUsPageAfterOurVisionSectionResource($afterOurVisionSection),
        ]);
    }

    /**
     * Show the about us page after our vision section (if active).
     */
    public function show(): JsonResponse
    {
        $afterOurVisionSection = AboutUsPageAfterOurVisionSection::active();

        if (! $afterOurVisionSection) {
            return response()->json([
                'message' => 'No active about us page after our vision section found',
            ], 404);
        }

        return response()->json([
            'after_our_vision_section' => new AboutUsPageAfterOurVisionSectionResource($afterOurVisionSection),
        ]);
    }

    /**
     * Show a specific about us page after our vision section.
     */
    public function showById(AboutUsPageAfterOurVisionSection $aboutUsPageAfterOurVisionSection): JsonResponse
    {
        return response()->json([
            'after_our_vision_section' => new AboutUsPageAfterOurVisionSectionResource($aboutUsPageAfterOurVisionSection),
        ]);
    }

    /**
     * Create or update the single about us page after our vision section.
     */
    public function store(CreateAboutUsPageAfterOurVisionSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $afterOurVisionSection = AboutUsPageAfterOurVisionSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $afterOurVisionSection);

        if ($afterOurVisionSection) {
            // Update existing record
            $afterOurVisionSection->update($data);

            return response()->json([
                'message' => 'About us page after our vision section updated successfully',
                'after_our_vision_section' => new AboutUsPageAfterOurVisionSectionResource($afterOurVisionSection->fresh()),
            ]);
        }

        // Create new record
        $afterOurVisionSection = AboutUsPageAfterOurVisionSection::create($data);

        return response()->json([
            'message' => 'About us page after our vision section created successfully',
            'after_our_vision_section' => new AboutUsPageAfterOurVisionSectionResource($afterOurVisionSection),
        ], 201);
    }

    /**
     * Update a about us page after our vision section by ID.
     */
    public function update(UpdateAboutUsPageAfterOurVisionSectionRequest $request, AboutUsPageAfterOurVisionSection $aboutUsPageAfterOurVisionSection): JsonResponse
    {
        // Get all fillable fields from request - check each field individually for form data
        $data = [];
        $fillableFields = ['title', 'paragraph', 'image_1', 'image_2', 'image_3', 'image_4', 'status'];
        $requestData = $request->all();

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle images
        $data = $this->processFileUploads($data, $request, $aboutUsPageAfterOurVisionSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $aboutUsPageAfterOurVisionSection->update($data);
        }

        return response()->json([
            'message' => 'About us page after our vision section updated successfully',
            'after_our_vision_section' => new AboutUsPageAfterOurVisionSectionResource($aboutUsPageAfterOurVisionSection->fresh()),
        ]);
    }

    /**
     * Delete a about us page after our vision section.
     */
    public function destroy(AboutUsPageAfterOurVisionSection $aboutUsPageAfterOurVisionSection): JsonResponse
    {
        $aboutUsPageAfterOurVisionSection->delete();

        return response()->json([
            'message' => 'About us page after our vision section deleted successfully',
        ]);
    }

    /**
     * Toggle about us page after our vision section status between active and inactive.
     */
    public function setActive(AboutUsPageAfterOurVisionSection $aboutUsPageAfterOurVisionSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $aboutUsPageAfterOurVisionSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $aboutUsPageAfterOurVisionSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page after our vision section set as active successfully'
            : 'About us page after our vision section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            'after_our_vision_section' => new AboutUsPageAfterOurVisionSectionResource($aboutUsPageAfterOurVisionSection->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPageAfterOurVisionSectionRequest|UpdateAboutUsPageAfterOurVisionSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?AboutUsPageAfterOurVisionSection $afterOurVisionSection = null): array
    {
        $imageFields = ['image_1', 'image_2', 'image_3', 'image_4'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if updating
                if ($afterOurVisionSection && $afterOurVisionSection->$field) {
                    $oldPath = str_replace('/storage/', '', $afterOurVisionSection->$field);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $file = $request->file($field);
                $path = $file->store('about-us-page-after-our-vision-sections', 'public');
                $data[$field] = '/storage/'.$path;
            } elseif (isset($data[$field]) && is_string($data[$field])) {
                // If image is provided as a string URL, use it as is
                // No need to change it
            } elseif (! isset($data[$field]) && $afterOurVisionSection) {
                // Only preserve existing image if not provided at all
                $data[$field] = $afterOurVisionSection->$field;
            }
        }

        return $data;
    }
}
