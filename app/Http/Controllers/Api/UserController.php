<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\SuspendUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
     * Create a new user.
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated);

        // Generate identity number if not provided
        if (empty($data['identity_number'])) {
            $data['identity_number'] = $this->generateIdentityNumber();
        }

        // Hash password
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Remove password_confirmation and role_ids from data
        unset($data['password_confirmation'], $data['role_ids']);

        // Create user
        $user = User::create($data);

        // Attach roles if provided
        if ($request->has('role_ids') && is_array($request->role_ids)) {
            $user->roles()->sync($request->role_ids);
        }

        $user->load('roles');

        return $this->successResponse('User created successfully', new UserResource($user), 201);
    }

    /**
     * Update a user.
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $validated = $request->validated();
        $data = $this->processFileUploads($request, $validated);

        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Remove password_confirmation and role_ids from data
        unset($data['password_confirmation'], $data['role_ids']);

        // Only update fillable fields
        $updateData = [];
        foreach ($user->getFillable() as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (! empty($updateData)) {
            $user->update($updateData);
        }

        // Sync roles if provided
        if ($request->has('role_ids') && is_array($request->role_ids)) {
            $user->roles()->sync($request->role_ids);
        }

        $user->load('roles');

        return $this->successResponse('User updated successfully', new UserResource($user));
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): JsonResponse
    {
        // Delete image if exists
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return $this->successResponse('User deleted successfully');
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

    /**
     * Handle image upload.
     */
    private function processFileUploads(CreateUserRequest|UpdateUserRequest $request, array $data): array
    {
        if ($request->hasFile('image')) {
            // Delete old image if exists (for updates)
            $user = $request->route('user');
            if ($user !== null && $user->image) {
                $oldImage = $user->image;
                if (Storage::disk('public')->exists($oldImage)) {
                    Storage::disk('public')->delete($oldImage);
                }
            }

            $path = $request->file('image')->store('users', 'public');
            $data['image'] = $path;
        }

        return $data;
    }

    /**
     * Generate a unique identity number.
     */
    private function generateIdentityNumber(): string
    {
        do {
            $identityNumber = 'STF'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (User::where('identity_number', $identityNumber)->exists());

        return $identityNumber;
    }
}
