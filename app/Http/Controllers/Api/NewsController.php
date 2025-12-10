<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\News\CreateNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    use ApiResponse;

    /**
     * List all news.
     */
    public function index(): JsonResponse
    {
        $news = News::paginate(10);
        $resourceCollection = NewsResource::collection($news);

        return $this->paginatedResponse('News retrieved successfully', $news, $resourceCollection);
    }

    /**
     * Show a specific news.
     */
    public function show(News $news): JsonResponse
    {
        return $this->successResponse('News retrieved successfully', new NewsResource($news));
    }

    /**
     * Create a new news.
     */
    public function store(CreateNewsRequest $request): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated());

        $news = News::create($data);

        return $this->successResponse('News created successfully', new NewsResource($news), 201);
    }

    /**
     * Update a news.
     */
    public function update(UpdateNewsRequest $request, News $news): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated(), $news);

        $updateData = [];
        foreach ($news->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $news->update($updateData);
        }

        return $this->successResponse('News updated successfully', new NewsResource($news));
    }

    /**
     * Delete a news.
     */
    public function destroy(News $news): JsonResponse
    {
        // Delete feature_image if exists
        if ($news->feature_image && Storage::disk('public')->exists($news->feature_image)) {
            Storage::disk('public')->delete($news->feature_image);
        }

        // Delete author_image if exists
        if ($news->author_image && Storage::disk('public')->exists($news->author_image)) {
            Storage::disk('public')->delete($news->author_image);
        }

        $news->delete();

        return $this->successResponse('News deleted successfully');
    }

    /**
     * Toggle news status between active and inactive.
     */
    public function setActive(News $news): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $news->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $news->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'News set as active successfully'
            : 'News set as inactive successfully';

        return $this->successResponse($statusMessage, new NewsResource($news->fresh()));
    }

    /**
     * Handle image uploads for feature_image and author_image.
     */
    private function processFileUploads(CreateNewsRequest|UpdateNewsRequest $request, array $data, ?News $news = null): array
    {
        // Handle feature_image upload
        if ($request->hasFile('feature_image')) {
            if ($news !== null && $news->feature_image) {
                $oldImage = $news->feature_image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('feature_image')->store('news/feature-images', 'public');
            $data['feature_image'] = $path;
        }

        // Handle author_image upload
        if ($request->hasFile('author_image')) {
            if ($news !== null && $news->author_image) {
                $oldImage = $news->author_image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('author_image')->store('news/author-images', 'public');
            $data['author_image'] = $path;
        }

        return $data;
    }
}
