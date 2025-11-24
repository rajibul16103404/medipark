<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SuspendUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * List all users.
     */
    public function index(): AnonymousResourceCollection
    {
        $users = User::with('roles')->get();

        return UserResource::collection($users);
    }

    /**
     * Show a specific user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load('roles');

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Suspend a user.
     */
    public function suspend(SuspendUserRequest $request, User $user): JsonResponse
    {
        if ($user->isSuspended()) {
            return response()->json([
                'message' => 'User is already suspended',
            ], 400);
        }

        $user->update([
            'suspended_at' => now(),
            'suspension_reason' => $request->reason,
        ]);

        $user->load('roles');

        return response()->json([
            'message' => 'User suspended successfully',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Unsuspend a user.
     */
    public function unsuspend(User $user): JsonResponse
    {
        if (! $user->isSuspended()) {
            return response()->json([
                'message' => 'User is not suspended',
            ], 400);
        }

        $user->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        $user->load('roles');

        return response()->json([
            'message' => 'User unsuspended successfully',
            'user' => new UserResource($user),
        ]);
    }
}
