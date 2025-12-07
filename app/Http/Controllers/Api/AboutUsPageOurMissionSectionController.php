<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutUsPageOurMissionSection\CreateAboutUsPageOurMissionSectionRequest;
use App\Http\Requests\AboutUsPageOurMissionSection\UpdateAboutUsPageOurMissionSectionRequest;
use App\Http\Resources\AboutUsPageOurMissionSectionResource;
use App\Models\AboutUsPageOurMissionSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutUsPageOurMissionSectionController extends Controller
{
    use ApiResponse;

    /**
     * Get the single about us page our mission section.
     */
    public function index(): JsonResponse
    {
        $ourMissionSection = AboutUsPageOurMissionSection::first();

        if (! $ourMissionSection) {
            return response()->json([
                'message' => 'No about us page our mission section found. You can create one using POST /api/about-us-page-our-mission-sections',
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => new AboutUsPageOurMissionSectionResource($ourMissionSection),
        ]);
    }

    /**
     * Show the about us page our mission section (if active).
     */
    public function show(): JsonResponse
    {
        $ourMissionSection = AboutUsPageOurMissionSection::active();

        if (! $ourMissionSection) {
            return response()->json([
                'message' => 'No active about us page our mission section found',
            ], 404);
        }

        return response()->json([
            'our_mission_section' => new AboutUsPageOurMissionSectionResource($ourMissionSection),
        ]);
    }

    /**
     * Show a specific about us page our mission section.
     */
    public function showById(AboutUsPageOurMissionSection $aboutUsPageOurMissionSection): JsonResponse
    {
        return response()->json([
            'our_mission_section' => new AboutUsPageOurMissionSectionResource($aboutUsPageOurMissionSection),
        ]);
    }

    /**
     * Create or update the single about us page our mission section.
     */
    public function store(CreateAboutUsPageOurMissionSectionRequest $request): JsonResponse
    {
        // Check if a record already exists
        $ourMissionSection = AboutUsPageOurMissionSection::first();

        $data = $this->processFileUploads($request->validated(), $request, $ourMissionSection);

        if ($ourMissionSection) {
            // Update existing record
            $ourMissionSection->update($data);

            return response()->json([
                'message' => 'About us page our mission section updated successfully',
                'our_mission_section' => new AboutUsPageOurMissionSectionResource($ourMissionSection->fresh()),
            ]);
        }

        // Create new record
        $ourMissionSection = AboutUsPageOurMissionSection::create($data);

        return response()->json([
            'message' => 'About us page our mission section created successfully',
            'our_mission_section' => new AboutUsPageOurMissionSectionResource($ourMissionSection),
        ], 201);
    }

    /**
     * Update a about us page our mission section by ID.
     */
    public function update(UpdateAboutUsPageOurMissionSectionRequest $request, AboutUsPageOurMissionSection $aboutUsPageOurMissionSection): JsonResponse
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
        $data = $this->processFileUploads($data, $request, $aboutUsPageOurMissionSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $aboutUsPageOurMissionSection->update($data);
        }

        return response()->json([
            'message' => 'About us page our mission section updated successfully',
            'our_mission_section' => new AboutUsPageOurMissionSectionResource($aboutUsPageOurMissionSection->fresh()),
        ]);
    }

    /**
     * Delete a about us page our mission section.
     */
    public function destroy(AboutUsPageOurMissionSection $aboutUsPageOurMissionSection): JsonResponse
    {
        $aboutUsPageOurMissionSection->delete();

        return response()->json([
            'message' => 'About us page our mission section deleted successfully',
        ]);
    }

    /**
     * Toggle about us page our mission section status between active and inactive.
     */
    public function setActive(AboutUsPageOurMissionSection $aboutUsPageOurMissionSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $aboutUsPageOurMissionSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $aboutUsPageOurMissionSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'About us page our mission section set as active successfully'
            : 'About us page our mission section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            'our_mission_section' => new AboutUsPageOurMissionSectionResource($aboutUsPageOurMissionSection->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateAboutUsPageOurMissionSectionRequest|UpdateAboutUsPageOurMissionSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?AboutUsPageOurMissionSection $ourMissionSection = null): array
    {
        $requestData = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old file if updating
            if ($ourMissionSection && $ourMissionSection->image) {
                $oldPath = str_replace('/storage/', '', $ourMissionSection->image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('image');
            $path = $file->store('about-us-page-our-mission-sections', 'public');
            $data['image'] = '/storage/'.$path;
        } elseif (array_key_exists('image', $requestData) && is_string($request->input('image'))) {
            // If image is provided as a string URL in form data, use it
            $data['image'] = $request->input('image');
        } elseif (! array_key_exists('image', $requestData) && ! $request->hasFile('image') && $ourMissionSection) {
            // Only preserve existing image if not provided at all
            $data['image'] = $ourMissionSection->image;
        }

        return $data;
    }
}
