<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SuspendUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * List all users.
     */
    public function index(): JsonResponse
    {
        $users = User::with('roles')->paginate(10);
        $resourceCollection = UserResource::collection($users);

        return $this->paginatedResponse('Users retrieved successfully', $users, $resourceCollection);
    }

    /**
     * Show a specific user.
     */
    public function show(User $user): JsonResponse
    {
        $user->load('roles');

        return $this->successResponse('User retrieved successfully', new UserResource($user));
    }

    /**
     * Suspend a user.
     */
    public function suspend(SuspendUserRequest $request, User $user): JsonResponse
    {
        if ($user->isSuspended()) {
            return $this->errorResponse('User is already suspended', 400);
        }

        $user->update([
            'suspended_at' => now(),
            'suspension_reason' => $request->reason,
        ]);

        $user->load('roles');

        return $this->successResponse('User suspended successfully', new UserResource($user));
    }

    /**
     * Unsuspend a user.
     */
    public function unsuspend(User $user): JsonResponse
    {
        if (! $user->isSuspended()) {
            return $this->errorResponse('User is not suspended', 400);
        }

        $user->update([
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        $user->load('roles');

        return $this->successResponse('User unsuspended successfully', new UserResource($user));
    }
}
