<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPageOurVisionSection\CreateAboutUsPageOurVisionSectionRequest;
use App\Http\Requests\AboutUsPageOurVisionSection\UpdateAboutUsPageOurVisionSectionRequest;
use App\Http\Resources\AboutUsPageOurVisionSectionResource;
use App\Models\AboutUsPageOurVisionSection;
use App\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsPageOurVisionSectionController extends Controller
{
    /**
     * Get the single about us page our vision section.
     */
    public function index(): JsonResponse
    {
        $ourVisionSection = AboutUsPageOurVisionSection::first();

        if (! $ourVisionSection) {
            return response()->json([
                'message' => 'No about us page our vision section found. You can create one using POST /api/about-us-page-our-vision-sections',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new AboutUsPageOurVisionSectionResource($ourVisionSection),
        ]);
    }

    /**
     * Show the about us page our vision section (if active).
     */
    public function show(): JsonResponse
    {
        $ourVisionSection = AboutUsPageOurVisionSection::active();

        if (! $ourVisionSection) {
            return response()->json([
                'message' => 'No active about us page our vision section found',
            ], 404);
        }

        return response()->json([
            'our_vision_section' => new AboutUsPageOurVisionSectionResource($ourVisionSection),
        ]);
    }

    /**
     * Show a specific about us page our vision section.
     */
    public function showById(AboutUsPageOurVisionSection $aboutUsPageOurVisionSection): JsonResponse
    {
        return response()->json([
            'our_vision_section' => new AboutUsPageOurVisionSectionResource($aboutUsPageOurVisionSection),
        ]);
    }

    /**
     * Create or update the single about us page our vision section.
     */
    public function store(CreateAboutUsPageOurVisionSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $ourVisionSection = AboutUsPageOurVisionSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $ourVisionSection);

        if ($ourVisionSection) {
            // Update existing record
            $ourVisionSection->update($data);

            return response()->json([
                'message' => 'About us page our vision section updated successfully',
                'our_vision_section' => new AboutUsPageOurVisionSectionResource($ourVisionSection->fresh()),
            ]);
        }

        // Create new record
        $ourVisionSection = AboutUsPageOurVisionSection::create($data);

        return response()->json([
            'message' => 'About us page our vision section created successfully',
            'our_vision_section' => new AboutUsPageOurVisionSectionResource($ourVisionSection),
        ], 201);
    }

    /**
     * Update a about us page our vision section by ID.
     */
    public function update(UpdateAboutUsPageOurVisionSectionRequest $request, AboutUsPageOurVisionSection $aboutUsPageOurVisionSection): JsonResponse
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
        $data = $this->processFileUploads($data, $request, $aboutUsPageOurVisionSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $aboutUsPageOurVisionSection->update($data);
        }

        return response()->json([
            'message' => 'About us page our vision section updated successfully',
            'our_vision_section' => new AboutUsPageOurVisionSectionResource($aboutUsPageOurVisionSection->fresh()),
        ]);
    }

    /**
     * Delete a about us page our vision section.
     */
    public function destroy(AboutUsPageOurVisionSection $aboutUsPageOurVisionSection): JsonResponse
    {
        $aboutUsPageOurVisionSection->delete();

        return response()->json([
            'message' => 'About us page our vision section deleted successfully',
        ]);
    }

    /**
     * Toggle about us page our vision section status between active and inactive.
     */
    public function setActive(AboutUsPageOurVisionSection $aboutUsPageOurVisionSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $aboutUsPageOurVisionSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $aboutUsPageOurVisionSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page our vision section set as active successfully'
            : 'About us page our vision section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            'our_vision_section' => new AboutUsPageOurVisionSectionResource($aboutUsPageOurVisionSection->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPageOurVisionSectionRequest|UpdateAboutUsPageOurVisionSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?AboutUsPageOurVisionSection $ourVisionSection = null): array
    {
        $requestData = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old file if updating
            if ($ourVisionSection && $ourVisionSection->image) {
                $oldPath = str_replace('/storage/', '', $ourVisionSection->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('image');
            $path = $file->store('about-us-page-our-vision-sections', 'public');
            $data['image'] = '/storage/'.$path;
        } elseif (array_key_exists('image', $requestData) && is_string($request->input('image'))) {
            // If image is provided as a string URL in form data, use it
            $data['image'] = $request->input('image');
        } elseif (! array_key_exists('image', $requestData) && ! $request->hasFile('image') && $ourVisionSection) {
            // Only preserve existing image if not provided at all
            $data['image'] = $ourVisionSection->image;
        }

        return $data;
    }
}
