<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPageWhoWeAreSection\CreateAboutUsPageWhoWeAreSectionRequest;
use App\Http\Requests\AboutUsPageWhoWeAreSection\UpdateAboutUsPageWhoWeAreSectionRequest;
use App\Http\Resources\AboutUsPageWhoWeAreSectionResource;
use App\Models\AboutUsPageWhoWeAreSection;
use App\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsPageWhoWeAreSectionController extends Controller
{
    /**
     * Get the single about us page who we are section.
     */
    public function index(): JsonResponse
    {
        $whoWeAreSection = AboutUsPageWhoWeAreSection::first();

        if (! $whoWeAreSection) {
            return response()->json([
                'message' => 'No about us page who we are section found. You can create one using POST /api/about-us-page-who-we-are-sections',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new AboutUsPageWhoWeAreSectionResource($whoWeAreSection),
        ]);
    }

    /**
     * Show the about us page who we are section (if active).
     */
    public function show(): JsonResponse
    {
        $whoWeAreSection = AboutUsPageWhoWeAreSection::active();

        if (! $whoWeAreSection) {
            return response()->json([
                'message' => 'No active about us page who we are section found',
            ], 404);
        }

        return response()->json([
            'who_we_are_section' => new AboutUsPageWhoWeAreSectionResource($whoWeAreSection),
        ]);
    }

    /**
     * Show a specific about us page who we are section.
     */
    public function showById(AboutUsPageWhoWeAreSection $aboutUsPageWhoWeAreSection): JsonResponse
    {
        return response()->json([
            'who_we_are_section' => new AboutUsPageWhoWeAreSectionResource($aboutUsPageWhoWeAreSection),
        ]);
    }

    /**
     * Create or update the single about us page who we are section.
     */
    public function store(CreateAboutUsPageWhoWeAreSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $whoWeAreSection = AboutUsPageWhoWeAreSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $whoWeAreSection);

        if ($whoWeAreSection) {
            // Update existing record
            $whoWeAreSection->update($data);

            return response()->json([
                'message' => 'About us page who we are section updated successfully',
                'who_we_are_section' => new AboutUsPageWhoWeAreSectionResource($whoWeAreSection->fresh()),
            ]);
        }

        // Create new record
        $whoWeAreSection = AboutUsPageWhoWeAreSection::create($data);

        return response()->json([
            'message' => 'About us page who we are section created successfully',
            'who_we_are_section' => new AboutUsPageWhoWeAreSectionResource($whoWeAreSection),
        ], 201);
    }

    /**
     * Update a about us page who we are section by ID.
     */
    public function update(UpdateAboutUsPageWhoWeAreSectionRequest $request, AboutUsPageWhoWeAreSection $aboutUsPageWhoWeAreSection): JsonResponse
    {
        // Get all fillable fields from request - check each field individually for form data
        $data = [];
        $fillableFields = ['title', 'paragraph', 'image_1', 'image_2', 'image_3', 'status'];
        $requestData = $request->all();

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle images
        $data = $this->processFileUploads($data, $request, $aboutUsPageWhoWeAreSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $aboutUsPageWhoWeAreSection->update($data);
        }

        return response()->json([
            'message' => 'About us page who we are section updated successfully',
            'who_we_are_section' => new AboutUsPageWhoWeAreSectionResource($aboutUsPageWhoWeAreSection->fresh()),
        ]);
    }

    /**
     * Delete a about us page who we are section.
     */
    public function destroy(AboutUsPageWhoWeAreSection $aboutUsPageWhoWeAreSection): JsonResponse
    {
        $aboutUsPageWhoWeAreSection->delete();

        return response()->json([
            'message' => 'About us page who we are section deleted successfully',
        ]);
    }

    /**
     * Toggle about us page who we are section status between active and inactive.
     */
    public function setActive(AboutUsPageWhoWeAreSection $aboutUsPageWhoWeAreSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $aboutUsPageWhoWeAreSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $aboutUsPageWhoWeAreSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page who we are section set as active successfully'
            : 'About us page who we are section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            'who_we_are_section' => new AboutUsPageWhoWeAreSectionResource($aboutUsPageWhoWeAreSection->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPageWhoWeAreSectionRequest|UpdateAboutUsPageWhoWeAreSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?AboutUsPageWhoWeAreSection $whoWeAreSection = null): array
    {
        $imageFields = ['image_1', 'image_2', 'image_3'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if updating
                if ($whoWeAreSection && $whoWeAreSection->$field) {
                    $oldPath = str_replace('/storage/', '', $whoWeAreSection->$field);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $file = $request->file($field);
                $path = $file->store('about-us-page-who-we-are-sections', 'public');
                $data[$field] = '/storage/'.$path;
            } elseif (isset($data[$field]) && is_string($data[$field])) {
                // If image is provided as a string URL, use it as is
                // No need to change it
            } elseif (! isset($data[$field]) && $whoWeAreSection) {
                // Only preserve existing image if not provided at all
                $data[$field] = $whoWeAreSection->$field;
            }
        }

        return $data;
    }
}
