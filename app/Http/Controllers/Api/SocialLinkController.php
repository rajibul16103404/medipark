<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialLink\CreateSocialLinkRequest;
use App\Http\Requests\SocialLink\UpdateSocialLinkRequest;
use App\Http\Resources\SocialLinkResource;
use App\Models\SocialLink;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SocialLinkController extends Controller
{
    use ApiResponse;

    /**
     * List all social links.
     */
    public function index(): JsonResponse
    {
        $socialLinks = SocialLink::paginate(10);
        $resourceCollection = SocialLinkResource::collection($socialLinks);

        return $this->paginatedResponse('Social links retrieved successfully', $socialLinks, $resourceCollection);
    }

    /**
     * Show a specific social link.
     */
    public function show(SocialLink $socialLink): JsonResponse
    {
        return $this->successResponse('Social link retrieved successfully', new SocialLinkResource($socialLink));
    }

    /**
     * Create a new social link.
     */
    public function store(CreateSocialLinkRequest $request): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated());

        $socialLink = SocialLink::create($data);

        return $this->successResponse('Social link created successfully', new SocialLinkResource($socialLink), 201);
    }

    /**
     * Update a social link.
     */
    public function update(UpdateSocialLinkRequest $request, SocialLink $socialLink): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated(), $socialLink);

        $updateData = [];
        foreach ($socialLink->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $socialLink->update($updateData);
        }

        return $this->successResponse('Social link updated successfully', new SocialLinkResource($socialLink));
    }

    /**
     * Delete a social link.
     */
    public function destroy(SocialLink $socialLink): JsonResponse
    {
        if ($socialLink->image && Storage::disk('public')->exists($socialLink->image)) {
            Storage::disk('public')->delete($socialLink->image);
        }

        $socialLink->delete();

        return $this->successResponse('Social link deleted successfully');
    }

    /**
     * Toggle social link status between active and inactive.
     */
    public function setActive(SocialLink $socialLink): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $socialLink->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $socialLink->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Social link set as active successfully'
            : 'Social link set as inactive successfully';

        return $this->successResponse($statusMessage, new SocialLinkResource($socialLink->fresh()));
    }

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateSocialLinkRequest|UpdateSocialLinkRequest $request, array $data, ?SocialLink $socialLink = null): array
    {
        if ($request->hasFile('image')) {
            if ($socialLink !== null && $socialLink->image) {
                $oldImage = $socialLink->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('social-links', 'public');
            $data['image'] = $path;
        }

        return $data;
    }
}
