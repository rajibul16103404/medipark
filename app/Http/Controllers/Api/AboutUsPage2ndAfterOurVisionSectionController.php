<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPage2ndAfterOurVisionSection\CreateAboutUsPage2ndAfterOurVisionSectionRequest;
use App\Http\Requests\AboutUsPage2ndAfterOurVisionSection\UpdateAboutUsPage2ndAfterOurVisionSectionRequest;
use App\Http\Resources\AboutUsPage2ndAfterOurVisionSectionResource;
use App\Models\AboutUsPage2ndAfterOurVisionSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsPage2ndAfterOurVisionSectionController extends Controller
{
    use ApiResponse;

    /**
     * Get the single about us page 2nd after our vision section.
     */
    public function index(): JsonResponse
    {
        $secondAfterOurVisionSection = AboutUsPage2ndAfterOurVisionSection::first();

        if (! $secondAfterOurVisionSection) {
            return response()->json([
                'message' => 'No about us page 2nd after our vision section found. You can create one using POST /api/about-us-page-2nd-after-our-vision-sections',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new AboutUsPage2ndAfterOurVisionSectionResource($secondAfterOurVisionSection),
        ]);
    }

    /**
     * Show the about us page 2nd after our vision section (if active).
     */
    public function show(): JsonResponse
    {
        $secondAfterOurVisionSection = AboutUsPage2ndAfterOurVisionSection::active();

        if (! $secondAfterOurVisionSection) {
            return response()->json([
                'message' => 'No active about us page 2nd after our vision section found',
            ], 404);
        }

        return response()->json([
            '2nd_after_our_vision_section' => new AboutUsPage2ndAfterOurVisionSectionResource($secondAfterOurVisionSection),
        ]);
    }

    /**
     * Show a specific about us page 2nd after our vision section.
     */
    public function showById(AboutUsPage2ndAfterOurVisionSection $section): JsonResponse
    {
        return response()->json([
            '2nd_after_our_vision_section' => new AboutUsPage2ndAfterOurVisionSectionResource($section),
        ]);
    }

    /**
     * Create or update the single about us page 2nd after our vision section.
     */
    public function store(CreateAboutUsPage2ndAfterOurVisionSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $secondAfterOurVisionSection = AboutUsPage2ndAfterOurVisionSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $secondAfterOurVisionSection);

        if ($secondAfterOurVisionSection) {
            // Update existing record
            $secondAfterOurVisionSection->update($data);

            return response()->json([
                'message' => 'About us page 2nd after our vision section updated successfully',
                '2nd_after_our_vision_section' => new AboutUsPage2ndAfterOurVisionSectionResource($secondAfterOurVisionSection->fresh()),
            ]);
        }

        // Create new record
        $secondAfterOurVisionSection = AboutUsPage2ndAfterOurVisionSection::create($data);

        return response()->json([
            'message' => 'About us page 2nd after our vision section created successfully',
            '2nd_after_our_vision_section' => new AboutUsPage2ndAfterOurVisionSectionResource($secondAfterOurVisionSection),
        ], 201);
    }

    /**
     * Update a about us page 2nd after our vision section by ID.
     */
    public function update(UpdateAboutUsPage2ndAfterOurVisionSectionRequest $request, AboutUsPage2ndAfterOurVisionSection $section): JsonResponse
    {
        // Get all fillable fields from request - check each field individually for form data
        $data = [];
        $fillableFields = ['title', 'paragraph', 'image', 'status'];
        $requestData = $request->all();

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle image
        $data = $this->processFileUploads($data, $request, $section);

        // Only update if we have data to update
        if (! empty($data)) {
            $section->update($data);
        }

        return response()->json([
            'message' => 'About us page 2nd after our vision section updated successfully',
            '2nd_after_our_vision_section' => new AboutUsPage2ndAfterOurVisionSectionResource($section->fresh()),
        ]);
    }

    /**
     * Delete a about us page 2nd after our vision section.
     */
    public function destroy(AboutUsPage2ndAfterOurVisionSection $section): JsonResponse
    {
        $section->delete();

        return response()->json([
            'message' => 'About us page 2nd after our vision section deleted successfully',
        ]);
    }

    /**
     * Toggle about us page 2nd after our vision section status between active and inactive.
     */
    public function setActive(AboutUsPage2ndAfterOurVisionSection $section): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $section->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $section->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page 2nd after our vision section set as active successfully'
            : 'About us page 2nd after our vision section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            '2nd_after_our_vision_section' => new AboutUsPage2ndAfterOurVisionSectionResource($section->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPage2ndAfterOurVisionSectionRequest|UpdateAboutUsPage2ndAfterOurVisionSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?AboutUsPage2ndAfterOurVisionSection $secondAfterOurVisionSection = null): array
    {
        $requestData = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old file if updating
            if ($secondAfterOurVisionSection && $secondAfterOurVisionSection->image) {
                $oldPath = str_replace('/storage/', '', $secondAfterOurVisionSection->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('image');
            $path = $file->store('about-us-page-2nd-after-our-vision-sections', 'public');
            $data['image'] = '/storage/'.$path;
        } elseif (array_key_exists('image', $requestData) && is_string($request->input('image'))) {
            // If image is provided as a string URL in form data, use it
            $data['image'] = $request->input('image');
        } elseif (! array_key_exists('image', $requestData) && ! $request->hasFile('image') && $secondAfterOurVisionSection) {
            // Only preserve existing image if not provided at all
            $data['image'] = $secondAfterOurVisionSection->image;
        }

        return $data;
    }
}
