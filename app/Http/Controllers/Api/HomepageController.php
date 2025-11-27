<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Homepage\CreateHomepageRequest;
use App\Http\Requests\Homepage\UpdateHomepageRequest;
use App\Http\Resources\HomepageResource;
use App\Models\Homepage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HomepageController extends Controller
{
    /**
     * List all homepages.
     */
    public function index(): AnonymousResourceCollection
    {
        $homepages = Homepage::latest()->get();

        return HomepageResource::collection($homepages);
    }

    /**
     * Show the active homepage.
     */
    public function show(): JsonResponse
    {
        $homepage = Homepage::active();

        if (! $homepage) {
            return response()->json([
                'message' => 'No active homepage found',
            ], 404);
        }

        return response()->json([
            'homepage' => new HomepageResource($homepage),
        ]);
    }

    /**
     * Show a specific homepage.
     */
    public function showById(Homepage $homepage): JsonResponse
    {
        return response()->json([
            'homepage' => new HomepageResource($homepage),
        ]);
    }

    /**
     * Create a new homepage.
     */
    public function store(CreateHomepageRequest $request): JsonResponse
    {
        // If setting as active, deactivate all other homepages
        if ($request->boolean('is_active')) {
            Homepage::where('is_active', true)->update(['is_active' => false]);
        }

        $homepage = Homepage::create($request->validated());

        return response()->json([
            'message' => 'Homepage created successfully',
            'homepage' => new HomepageResource($homepage),
        ], 201);
    }

    /**
     * Update a homepage.
     */
    public function update(UpdateHomepageRequest $request, Homepage $homepage): JsonResponse
    {
        // If setting as active, deactivate all other homepages
        if ($request->boolean('is_active') && ! $homepage->is_active) {
            Homepage::where('id', '!=', $homepage->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $homepage->update($request->validated());

        return response()->json([
            'message' => 'Homepage updated successfully',
            'homepage' => new HomepageResource($homepage->fresh()),
        ]);
    }

    /**
     * Delete a homepage.
     */
    public function destroy(Homepage $homepage): JsonResponse
    {
        $homepage->delete();

        return response()->json([
            'message' => 'Homepage deleted successfully',
        ]);
    }

    /**
     * Set a homepage as active.
     */
    public function setActive(Homepage $homepage): JsonResponse
    {
        // Deactivate all other homepages
        Homepage::where('id', '!=', $homepage->id)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $homepage->update(['is_active' => true]);

        return response()->json([
            'message' => 'Homepage set as active successfully',
            'homepage' => new HomepageResource($homepage->fresh()),
        ]);
    }
}
