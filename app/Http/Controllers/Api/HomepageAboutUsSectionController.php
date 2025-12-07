<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepageAboutUsSection\CreateHomepageAboutUsSectionRequest;
use App\Http\Requests\HomepageAboutUsSection\UpdateHomepageAboutUsSectionRequest;
use App\Http\Resources\HomepageAboutUsSectionResource;
use App\Models\HomepageAboutUsSection;
use App\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomepageAboutUsSectionController extends Controller
{
    /**
     * Get the single homepage about us section.
     */
    public function index(): JsonResponse
    {
        $aboutUsSection = HomepageAboutUsSection::first();

        if (! $aboutUsSection) {
            return response()->json([
                'message' => 'No homepage about us section found. You can create one using POST /api/homepage-about-us-sections',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new HomepageAboutUsSectionResource($aboutUsSection),
        ]);
    }

    /**
     * Show the homepage about us section (if active).
     */
    public function show(): JsonResponse
    {
        $aboutUsSection = HomepageAboutUsSection::active();

        if (! $aboutUsSection) {
            return response()->json([
                'message' => 'No active homepage about us section found',
            ], 404);
        }

        return response()->json([
            'about_us_section' => new HomepageAboutUsSectionResource($aboutUsSection),
        ]);
    }

    /**
     * Show a specific homepage about us section.
     */
    public function showById(HomepageAboutUsSection $homepageAboutUsSection): JsonResponse
    {
        return response()->json([
            'about_us_section' => new HomepageAboutUsSectionResource($homepageAboutUsSection),
        ]);
    }

    /**
     * Create or update the single homepage about us section.
     */
    public function store(CreateHomepageAboutUsSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $aboutUsSection = HomepageAboutUsSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $aboutUsSection);

        if ($aboutUsSection) {
            // Update existing record
            $aboutUsSection->update($data);

            return response()->json([
                'message' => 'Homepage about us section updated successfully',
                'about_us_section' => new HomepageAboutUsSectionResource($aboutUsSection->fresh()),
            ]);
        }

        // Create new record
        $aboutUsSection = HomepageAboutUsSection::create($data);

        return response()->json([
            'message' => 'Homepage about us section created successfully',
            'about_us_section' => new HomepageAboutUsSectionResource($aboutUsSection),
        ], 201);
    }

    /**
     * Update a homepage about us section by ID.
     */
    public function update(UpdateHomepageAboutUsSectionRequest $request, HomepageAboutUsSection $homepageAboutUsSection): JsonResponse
    {
        // Get all fillable fields from request - check each field individually for form data
        $data = [];
        $fillableFields = ['title', 'sub_title', 'content', 'image_1', 'image_2', 'image_3', 'status'];
        $requestData = $request->all();

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle images
        $data = $this->processFileUploads($data, $request, $homepageAboutUsSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $homepageAboutUsSection->update($data);
        }

        return response()->json([
            'message' => 'Homepage about us section updated successfully',
            'about_us_section' => new HomepageAboutUsSectionResource($homepageAboutUsSection->fresh()),
        ]);
    }

    /**
     * Delete a homepage about us section.
     */
    public function destroy(HomepageAboutUsSection $homepageAboutUsSection): JsonResponse
    {
        $homepageAboutUsSection->delete();

        return response()->json([
            'message' => 'Homepage about us section deleted successfully',
        ]);
    }

    /**
     * Toggle homepage about us section status between active and inactive.
     */
    public function setActive(HomepageAboutUsSection $homepageAboutUsSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $homepageAboutUsSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $homepageAboutUsSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Homepage about us section set as active successfully'
            : 'Homepage about us section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            'about_us_section' => new HomepageAboutUsSectionResource($homepageAboutUsSection->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateHomepageAboutUsSectionRequest|UpdateHomepageAboutUsSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?HomepageAboutUsSection $aboutUsSection = null): array
    {
        $imageFields = ['image_1', 'image_2', 'image_3'];

        foreach ($imageFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if updating
                if ($aboutUsSection && $aboutUsSection->$field) {
                    $oldPath = str_replace('/storage/', '', $aboutUsSection->$field);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $file = $request->file($field);
                $path = $file->store('homepage-about-us-sections', 'public');
                $data[$field] = '/storage/'.$path;
            } elseif (isset($data[$field]) && is_string($data[$field])) {
                // If image is provided as a string URL, use it as is
                // No need to change it
            } elseif (! isset($data[$field]) && $aboutUsSection) {
                // Only preserve existing image if not provided at all
                $data[$field] = $aboutUsSection->$field;
            }
        }

        return $data;
    }
}
