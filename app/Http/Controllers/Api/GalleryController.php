<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Gallery\CreateGalleryRequest;
use App\Http\Requests\Gallery\UpdateGalleryRequest;
use App\Http\Resources\GalleryResource;
use App\Models\Gallery;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    use ApiResponse;

    /**
     * List all galleries.
     */
    public function index(): JsonResponse
    {
        $galleries = Gallery::paginate(10);
        $resourceCollection = GalleryResource::collection($galleries);

        return $this->paginatedResponse('Galleries retrieved successfully', $galleries, $resourceCollection);
    }

    /**
     * Show a specific gallery.
     */
    public function show(Gallery $gallery): JsonResponse
    {
        return $this->successResponse('Gallery retrieved successfully', new GalleryResource($gallery));
    }

    /**
     * Create a new gallery.
     */
    public function store(CreateGalleryRequest $request): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated());

        $gallery = Gallery::create($data);

        return $this->successResponse('Gallery created successfully', new GalleryResource($gallery), 201);
    }

    /**
     * Update a gallery.
     */
    public function update(UpdateGalleryRequest $request, Gallery $gallery): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated(), $gallery);

        $updateData = [];
        foreach ($gallery->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $gallery->update($updateData);
        }

        return $this->successResponse('Gallery updated successfully', new GalleryResource($gallery));
    }

    /**
     * Delete a gallery.
     */
    public function destroy(Gallery $gallery): JsonResponse
    {
        if ($gallery->image && Storage::disk('public')->exists($gallery->image)) {
            Storage::disk('public')->delete($gallery->image);
        }

        $gallery->delete();

        return $this->successResponse('Gallery deleted successfully');
    }

    /**
     * Toggle gallery status between active and inactive.
     */
    public function setActive(Gallery $gallery): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $gallery->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $gallery->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Gallery set as active successfully'
            : 'Gallery set as inactive successfully';

        return $this->successResponse($statusMessage, new GalleryResource($gallery->fresh()));
    }

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateGalleryRequest|UpdateGalleryRequest $request, array $data, ?Gallery $gallery = null): array
    {
        if ($request->hasFile('image')) {
            if ($gallery !== null && $gallery->image) {
                $oldImage = $gallery->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('galleries', 'public');
            $data['image'] = $path;
        }

        return $data;
    }
}
