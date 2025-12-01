<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(): JsonResponse
    {
        $user = auth('api')->user()->load('roles');

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update the authenticated user's profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = auth('api')->user();

        // Get validated data - this ensures all fields that passed validation are included
        $validated = $request->validated();

        // Get all fillable fields from request directly
        $data = [];

        // Get name if provided
        if (array_key_exists('name', $request->all())) {
            $data['name'] = $request->input('name');
        }

        // Get email if provided
        if (array_key_exists('email', $request->all())) {
            $data['email'] = $request->input('email');
        }

        // Update name and email if provided
        if (! empty($data)) {
            $user->update($data);
        }

        // Update password if provided (handle separately as it needs hashing)
        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Reload user with roles
        $user = $user->fresh()->load('roles');

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => new UserResource($user),
        ]);
    }
}
