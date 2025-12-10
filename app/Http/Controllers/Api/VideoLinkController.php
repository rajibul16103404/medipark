<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VideoLink\CreateVideoLinkRequest;
use App\Http\Resources\VideoLinkResource;
use App\Models\VideoLink;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VideoLinkController extends Controller
{
    use ApiResponse;

    /**
     * List video link (singleton - only one record exists).
     */
    public function index(): JsonResponse
    {
        $videoLink = VideoLink::first();

        if (! $videoLink) {
            return $this->errorResponse('Video link not found', 404);
        }

        return $this->successResponse('Video link retrieved successfully', new VideoLinkResource($videoLink));
    }

    /**
     * Get the video link (singleton - only one record exists).
     */
    public function show(): JsonResponse
    {
        $videoLink = VideoLink::first();

        if (! $videoLink) {
            return $this->errorResponse('Video link not found', 404);
        }

        return $this->successResponse('Video link retrieved successfully', new VideoLinkResource($videoLink));
    }

    /**
     * Create or update video link (singleton - only one record allowed).
     */
    public function store(CreateVideoLinkRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Process file uploads
        $data = $this->processFileUploads($validated, $request);

        // Get the existing video link or create a new one
        $videoLink = VideoLink::first();

        if ($videoLink) {
            // Update existing record
            // Delete old video if new one is being uploaded
            if ($request->hasFile('video') && $videoLink->video) {
                $oldPath = str_replace('/storage/', '', $videoLink->video);
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $videoLink->update($data);
            $message = 'Video link updated successfully';
            $statusCode = 200;
        } else {
            // Create new record
            $videoLink = VideoLink::create($data);
            $message = 'Video link created successfully';
            $statusCode = 201;
        }

        return $this->successResponse($message, new VideoLinkResource($videoLink->fresh()), $statusCode);
    }

    /**
     * Toggle video link status between active and inactive.
     */
    public function setActive(): JsonResponse
    {
        $videoLink = VideoLink::first();

        if (! $videoLink) {
            return $this->errorResponse('Video link not found', 404);
        }

        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $videoLink->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $videoLink->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Video link set as active successfully'
            : 'Video link set as inactive successfully';

        return $this->successResponse($statusMessage, new VideoLinkResource($videoLink->fresh()));
    }

    /**
     * Process file uploads and return data array with file paths.
     *
     * @param  array<string, mixed>  $data
     * @param  CreateVideoLinkRequest  $request
     * @return array<string, mixed>
     */
    protected function processFileUploads(array $data, $request): array
    {
        $requestData = $request->all();
        $videoLink = VideoLink::first();

        // Handle video upload
        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $path = $file->store('video-links', 'public');
            $data['video'] = '/storage/'.$path;
        } elseif (array_key_exists('video', $requestData) && is_string($request->input('video'))) {
            // If video is provided as a string URL in form data, use it
            $data['video'] = $request->input('video');
        } elseif (! array_key_exists('video', $requestData) && ! $request->hasFile('video') && $videoLink) {
            // Only preserve existing video if not provided at all
            $data['video'] = $videoLink->video;
        }

        return $data;
    }
}
