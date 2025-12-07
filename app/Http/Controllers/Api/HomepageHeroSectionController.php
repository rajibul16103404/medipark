<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepageHeroSection\CreateHomepageHeroSectionRequest;
use App\Http\Requests\HomepageHeroSection\UpdateHomepageHeroSectionRequest;
use App\Http\Resources\HomepageHeroSectionResource;
use App\Models\HomepageHeroSection;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HomepageHeroSectionController extends Controller
{
    use ApiResponse;

    /**
     * List all homepage hero sections.
     */
    public function index(): JsonResponse
    {
        $heroSections = HomepageHeroSection::paginate(10);
        $resourceCollection = HomepageHeroSectionResource::collection($heroSections);

        return $this->paginatedResponse('Homepage hero sections retrieved successfully', $heroSections, $resourceCollection);
    }

    /**
     * Show the active homepage hero section.
     */
    public function show(): JsonResponse
    {
        $heroSection = HomepageHeroSection::active();

        if (! $heroSection) {
            return $this->errorResponse('No active homepage hero section found', 404);
        }

        return $this->successResponse('Homepage hero section retrieved successfully', new HomepageHeroSectionResource($heroSection));
    }

    /**
     * Show a specific homepage hero section.
     */
    public function showById(HomepageHeroSection $homepageHeroSection): JsonResponse
    {
        return $this->successResponse('Homepage hero section retrieved successfully', new HomepageHeroSectionResource($homepageHeroSection));
    }

    /**
     * Create a new homepage hero section.
     */
    public function store(CreateHomepageHeroSectionRequest $request): JsonResponse
    {
        // If setting as active, deactivate all other hero sections
        if ($request->input('status') === Status::Active->value) {
            HomepageHeroSection::where('status', Status::Active->value)
                ->update(['status' => Status::Inactive->value]);
        }

        $data = $this->processFileUploads($request->validated(), $request);

        $heroSection = HomepageHeroSection::create($data);

        return $this->successResponse('Homepage hero section created successfully', new HomepageHeroSectionResource($heroSection), 201);
    }

    /**
     * Update a homepage hero section by ID.
     */
    public function update(UpdateHomepageHeroSectionRequest $request, HomepageHeroSection $homepageHeroSection): JsonResponse
    {
        // If setting as active, deactivate all other hero sections
        if ($request->input('status') === Status::Active->value && $homepageHeroSection->status !== Status::Active) {
            HomepageHeroSection::where('id', '!=', $homepageHeroSection->id)
                ->where('status', Status::Active->value)
                ->update(['status' => Status::Inactive->value]);
        }

        // Get all fillable fields from request - check each field individually for form data
        $data = [];
        $fillableFields = ['title', 'subtitle', 'opacity', 'serial', 'status'];
        $requestData = $request->all();

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $requestData)) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle background_image (checks hasFile separately)
        $data = $this->processFileUploads($data, $request, $homepageHeroSection);

        // Only update if we have data to update
        if (! empty($data)) {
            $homepageHeroSection->update($data);
        }

        return $this->successResponse('Homepage hero section updated successfully', new HomepageHeroSectionResource($homepageHeroSection->fresh()));
    }

    /**
     * Delete a homepage hero section.
     */
    public function destroy(HomepageHeroSection $homepageHeroSection): JsonResponse
    {
        $homepageHeroSection->delete();

        return $this->successResponse('Homepage hero section deleted successfully');
    }

    /**
     * Toggle homepage hero section status between active and inactive.
     */
    public function setActive(HomepageHeroSection $homepageHeroSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $homepageHeroSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        // If setting as active, deactivate all other hero sections
        if ($newStatus === Status::Active) {
            HomepageHeroSection::where('id', '!=', $homepageHeroSection->id)
                ->where('status', Status::Active->value)
                ->update(['status' => Status::Inactive->value]);
        }

        $homepageHeroSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Homepage hero section set as active successfully'
            : 'Homepage hero section set as inactive successfully';

        return $this->successResponse($statusMessage, new HomepageHeroSectionResource($homepageHeroSection->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateHomepageHeroSectionRequest|UpdateHomepageHeroSectionRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?HomepageHeroSection $heroSection = null): array
    {
        $requestData = $request->all();

        // Handle background_image upload
        if ($request->hasFile('background_image')) {
            // Delete old file if updating
            if ($heroSection && $heroSection->background_image) {
                $oldPath = str_replace('/storage/', '', $heroSection->background_image);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('background_image');
            $path = $file->store('homepage-hero-sections', 'public');
            $data['background_image'] = '/storage/'.$path;
        } elseif (array_key_exists('background_image', $requestData) && is_string($request->input('background_image'))) {
            // If background_image is provided as a string URL in form data, use it
            $data['background_image'] = $request->input('background_image');
        } elseif (! array_key_exists('background_image', $requestData) && ! $request->hasFile('background_image') && $heroSection) {
            // Only preserve existing background_image if not provided at all
            $data['background_image'] = $heroSection->background_image;
        }

        return $data;
    }
}
