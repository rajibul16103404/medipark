<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Blog\AssignFacilityRequest;
use App\Http\Requests\Blog\CreateBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Status;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    use ApiResponse;

    /**
     * List all blogs.
     */
    public function index(): JsonResponse
    {
        $blogs = Blog::with('facility')->paginate(10);
        $resourceCollection = BlogResource::collection($blogs);

        return $this->paginatedResponse('Blogs retrieved successfully', $blogs, $resourceCollection);
    }

    /**
     * Show a specific blog.
     */
    public function show(Blog $blog): JsonResponse
    {
        $blog->load('facility');

        return $this->successResponse('Blog retrieved successfully', new BlogResource($blog));
    }

    /**
     * Create a new blog.
     */
    public function store(CreateBlogRequest $request): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated());

        $blog = Blog::create($data);

        return $this->successResponse('Blog created successfully', new BlogResource($blog), 201);
    }

    /**
     * Update a blog.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): JsonResponse
    {
        $data = $this->processFileUploads($request, $request->validated(), $blog);

        $updateData = [];
        foreach ($blog->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $blog->update($updateData);
        }

        return $this->successResponse('Blog updated successfully', new BlogResource($blog));
    }

    /**
     * Delete a blog.
     */
    public function destroy(Blog $blog): JsonResponse
    {
        // Delete feature_image if exists
        if ($blog->feature_image && Storage::disk('public')->exists($blog->feature_image)) {
            Storage::disk('public')->delete($blog->feature_image);
        }

        // Delete author_image if exists
        if ($blog->author_image && Storage::disk('public')->exists($blog->author_image)) {
            Storage::disk('public')->delete($blog->author_image);
        }

        $blog->delete();

        return $this->successResponse('Blog deleted successfully');
    }

    /**
     * Toggle blog status between active and inactive.
     */
    public function setActive(Blog $blog): JsonResponse
    {
        // Toggle status: if active, make inactive; if inactive, make active
        $newStatus = $blog->status === Status::Active
            ? Status::Inactive
            : Status::Active;

        $blog->update(['status' => $newStatus->value]);

        $statusMessage = $newStatus === Status::Active
            ? 'Blog set as active successfully'
            : 'Blog set as inactive successfully';

        return $this->successResponse($statusMessage, new BlogResource($blog->fresh()));
    }

    /**
     * Handle image uploads for feature_image and author_image.
     */
    private function processFileUploads(CreateBlogRequest|UpdateBlogRequest $request, array $data, ?Blog $blog = null): array
    {
        // Handle feature_image upload
        if ($request->hasFile('feature_image')) {
            if ($blog !== null && $blog->feature_image) {
                $oldImage = $blog->feature_image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('feature_image')->store('blogs/feature-images', 'public');
            $data['feature_image'] = $path;
        }

        // Handle author_image upload
        if ($request->hasFile('author_image')) {
            if ($blog !== null && $blog->author_image) {
                $oldImage = $blog->author_image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('author_image')->store('blogs/author-images', 'public');
            $data['author_image'] = $path;
        }

        return $data;
    }

    /**
     * Assign a facility to a blog.
     */
    public function assignFacility(AssignFacilityRequest $request, Blog $blog): JsonResponse
    {
        $blog->update([
            'facility_id' => $request->facility_id,
        ]);

        $blog->load('facility');

        return $this->successResponse('Facility assigned to blog successfully', new BlogResource($blog));
    }
}
