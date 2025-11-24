<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignPrivilegesRequest;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{
    /**
     * List all roles.
     */
    public function index(): AnonymousResourceCollection
    {
        $roles = Role::with('privileges')->get();

        return RoleResource::collection($roles);
    }

    /**
     * Show a specific role.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('privileges');

        return response()->json([
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Create a new role.
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        $role = Role::create([
            'name' => $request->name,
            'slug' => $request->slug,
        ]);

        $role->load('privileges');

        return response()->json([
            'message' => 'Role created successfully',
            'role' => new RoleResource($role),
        ], 201);
    }

    /**
     * Update a role.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return response()->json([
                'message' => 'The admin role cannot be modified',
            ], 403);
        }

        $role->update($request->only(['name', 'slug']));
        $role->load('privileges');

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return response()->json([
                'message' => 'The admin role cannot be deleted',
            ], 403);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully',
        ]);
    }

    /**
     * Assign privileges to a role.
     */
    public function assignPrivileges(AssignPrivilegesRequest $request, Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return response()->json([
                'message' => 'Cannot assign privileges to the admin role. Admin role automatically has all privileges.',
            ], 403);
        }

        $privilegeIds = $request->privilege_ids;

        $role->privileges()->sync($privilegeIds);

        $role->load('privileges');

        return response()->json([
            'message' => 'Privileges assigned to role successfully',
            'role' => new RoleResource($role),
        ]);
    }

    /**
     * Remove privileges from a role.
     */
    public function removePrivileges(Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return response()->json([
                'message' => 'Cannot remove privileges from the admin role. Admin role automatically has all privileges.',
            ], 403);
        }

        $role->privileges()->detach();

        $role->load('privileges');

        return response()->json([
            'message' => 'All privileges removed from role successfully',
            'role' => new RoleResource($role),
        ]);
    }
}
