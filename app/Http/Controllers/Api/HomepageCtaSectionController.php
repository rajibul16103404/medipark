<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HomepageCtaSection\CreateHomepageCtaSectionRequest;
use App\Http\Requests\HomepageCtaSection\UpdateHomepageCtaSectionRequest;
use App\Http\Resources\HomepageCtaSectionResource;
use App\Models\HomepageCtaSection;
use App\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomepageCtaSectionController extends Controller
{
    /**
     * List all homepage CTA sections.
     */
    public function index(): JsonResponse|AnonymousResourceCollection
    {
        $ctaSections = HomepageCtaSection::all();

        if ($ctaSections->isEmpty()) {
            return response()->json([
                'message' => 'No homepage CTA sections found. You can create one using POST /api/homepage-cta-sections',
                'data' => [],
            ]);
        }

        return response()->json([
            'data' => HomepageCtaSectionResource::collection($ctaSections),
        ]);
    }

    /**
     * Show the active homepage CTA section.
     */
    public function show(): JsonResponse
    {
        $ctaSection = HomepageCtaSection::active();

        if (! $ctaSection) {
            return response()->json([
                'message' => 'No active homepage CTA section found',
            ], 404);
        }

        return response()->json([
            'cta_section' => new HomepageCtaSectionResource($ctaSection),
        ]);
    }

    /**
     * Show a specific homepage CTA section.
     */
    public function showById(HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        return response()->json([
            'cta_section' => new HomepageCtaSectionResource($homepageCtaSection),
        ]);
    }

    /**
     * Create a new homepage CTA section.
     */
    public function store(CreateHomepageCtaSectionRequest $request): JsonResponse
    {
        // If setting as active, deactivate all other CTA sections
        if ($request->input('status') === Status::Active->value) {
            HomepageCtaSection::where('status', Status::Active->value)
                ->update(['status' => Status::Inactive->value]);
        }

        $ctaSection = HomepageCtaSection::create($request->validated());

        return response()->json([
            'message' => 'Homepage CTA section created successfully',
            'cta_section' => new HomepageCtaSectionResource($ctaSection),
        ], 201);
    }

    /**
     * Update a homepage CTA section by ID.
     */
    public function update(UpdateHomepageCtaSectionRequest $request, HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        // If setting as active, deactivate all other CTA sections
        if ($request->input('status') === Status::Active->value && $homepageCtaSection->status !== Status::Active) {
            HomepageCtaSection::where('id', '!=', $homepageCtaSection->id)
                ->where('status', Status::Active->value)
                ->update(['status' => Status::Inactive->value]);
        }

        // Get all fillable fields from request - get directly from input to ensure all data is captured
        $data = [];
        $fillableFields = ['title', 'sub_title', 'content', 'button_text', 'button_link', 'status'];

        foreach ($fillableFields as $field) {
            if (array_key_exists($field, $request->all())) {
                $data[$field] = $request->input($field);
            }
        }

        $homepageCtaSection->update($data);

        return response()->json([
            'message' => 'Homepage CTA section updated successfully',
            'cta_section' => new HomepageCtaSectionResource($homepageCtaSection->fresh()),
        ]);
    }

    /**
     * Delete a homepage CTA section.
     */
    public function destroy(HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        $homepageCtaSection->delete();

        return response()->json([
            'message' => 'Homepage CTA section deleted successfully',
        ]);
    }

    /**
     * Toggle homepage CTA section status between active and inactive.
     */
    public function setActive(HomepageCtaSection $homepageCtaSection): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $homepageCtaSection->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        // If setting as active, deactivate all other CTA sections
        if ($newStatus === Status::Active) {
            HomepageCtaSection::where('id', '!=', $homepageCtaSection->id)
                ->where('status', Status::Active->value)
                ->update(['status' => Status::Inactive->value]);
        }

        $homepageCtaSection->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Homepage CTA section set as active successfully'
            : 'Homepage CTA section set as inactive successfully';

        return response()->json([
            'message' => $statusMessage,
            'cta_section' => new HomepageCtaSectionResource($homepageCtaSection->fresh()),
        ]);
    }
}
