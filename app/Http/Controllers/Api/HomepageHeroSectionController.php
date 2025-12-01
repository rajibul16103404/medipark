<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepageHeroSection\CreateHomepageHeroSectionRequest;
use App\Http\Requests\HomepageHeroSection\UpdateHomepageHeroSectionRequest;
use App\Http\Resources\HomepageHeroSectionResource;
use App\Models\HomepageHeroSection;
use App\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class HomepageHeroSectionController extends Controller
{
    /**
     * List all homepage hero sections.
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        $heroSections = HomepageHeroSection::all();

        if ($heroSections->isEmpty()) {
            return response()->json([
                'message' => 'No homepage hero sections found. You can create one using POST /api/homepage-hero-sections',
                'data' => [],
            ]);
        }

        return response()->json([
            'data' => HomepageHeroSectionResource::collection($heroSections),
        ]);
    }

    /**
     * Show the active homepage hero section.
     */
    public function show(): JsonResponse
    {
        $heroSection = HomepageHeroSection::active();

        if (! $heroSection) {
            return response()->json([
                'message' => 'No active homepage hero section found',
            ], 404);
        }

        return response()->json([
            'hero_section' => new HomepageHeroSectionResource($heroSection),
        ]);
    }

    /**
     * Show a specific homepage hero section.
     */
    public function showById(HomepageHeroSection $homepageHeroSection): JsonResponse
    {
        return response()->json([
            'hero_section' => new HomepageHeroSectionResource($homepageHeroSection),
        ]);
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

        return response()->json([
            'message' => 'Homepage hero section created successfully',
            'hero_section' => new HomepageHeroSectionResource($heroSection),
        ], 201);
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

        // Get all fillable fields from request - get directly from input to ensure all data is captured
        $data = [];
        $fillableFields = ['title', 'subtitle', 'opacity', 'serial', 'status', 'background_image'];

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $request->all())) {
                $data[$field] = $request->input($field);
            }
        }

        // Process file uploads and handle background_image
        $data = $this->processFileUploads($data, $request, $homepageHeroSection);

        $homepageHeroSection->update($data);

        return response()->json([
            'message' => 'Homepage hero section updated successfully',
            'hero_section' => new HomepageHeroSectionResource($homepageHeroSection->fresh()),
        ]);
    }

    /**
     * Delete a homepage hero section.
     */
    public function destroy(HomepageHeroSection $homepageHeroSection): JsonResponse
    {
        $homepageHeroSection->delete();

        return response()->json([
            'message' => 'Homepage hero section deleted successfully',
        ]);
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

        return response()->json([
            'message' => $statusMessage,
            'hero_section' => new HomepageHeroSectionResource($homepageHeroSection->fresh()),
        ]);
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateHomepageHeroSectionRequest|UpdateHomepageHeroSectionRequest  $request
     * @param  HomepageHeroSection|null  $heroSection
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request, ?HomepageHeroSection $heroSection = null): array
    {
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
        } elseif (isset($data['background_image']) && is_string($data['background_image'])) {
            // If background_image is provided as a string URL, use it as is
            // No need to change it
        } elseif (! isset($data['background_image']) && $heroSection) {
            // Only preserve existing background_image if not provided at all
            $data['background_image'] = $heroSection->background_image;
        }

        // All other fields from validated() will be updated directly
        // No need to preserve them - update all inputted data

        return $data;
    }
}
