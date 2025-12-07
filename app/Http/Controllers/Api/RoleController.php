<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignPrivilegesRequest;
use App\Http\Requests\Role\CreateRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Models\Privilege;
use App\Models\Role;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * List all roles.
     */
    public function index(): JsonResponse
    {
        $roles = Role::with('privileges')->paginate(10);
        $resourceCollection = RoleResource::collection($roles);

        return $this->paginatedResponse('Roles retrieved successfully', $roles, $resourceCollection);
    }

    /**
     * Show a specific role.
     */
    public function show(Role $role): JsonResponse
    {
        $role->load('privileges');

        return $this->successResponse('Role retrieved successfully', new RoleResource($role));
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

        return $this->successResponse('Role created successfully', new RoleResource($role), 201);
    }

    /**
     * Update a role.
     */
    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return $this->errorResponse('The admin role cannot be modified', 403);
        }

        $role->update($request->only(['name', 'slug']));
        $role->load('privileges');

        return $this->successResponse('Role updated successfully', new RoleResource($role));
    }

    /**
     * Delete a role.
     */
    public function destroy(Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return $this->errorResponse('The admin role cannot be deleted', 403);
        }

        $role->delete();

        return $this->successResponse('Role deleted successfully');
    }

    /**
     * Assign privileges to a role.
     */
    public function assignPrivileges(AssignPrivilegesRequest $request, Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return $this->errorResponse('Cannot assign privileges to the admin role. Admin role automatically has all privileges.', 403);
        }

        $privilegeIds = $request->privilege_ids;

        $role->privileges()->sync($privilegeIds);

        $role->load('privileges');

        return $this->successResponse('Privileges assigned to role successfully', new RoleResource($role));
    }

    /**
     * Remove all privileges from a role.
     */
    public function removePrivileges(Role $role): JsonResponse
    {
        if ($role->isAdmin()) {
            return $this->errorResponse('Cannot remove privileges from the admin role. Admin role automatically has all privileges.', 403);
        }

        $role->privileges()->detach();

        $role->load('privileges');

        return $this->successResponse('All privileges removed from role successfully', new RoleResource($role));
    }

    /**
     * Remove a single privilege from a role.
     */
    public function removePrivilege(Role $role, Privilege $privilege): JsonResponse
    {
        if ($role->isAdmin()) {
            return $this->errorResponse('Cannot remove privileges from the admin role. Admin role automatically has all privileges.', 403);
        }

        if (! $role->privileges()->where('privileges.id', $privilege->id)->exists()) {
            return $this->errorResponse('This privilege is not assigned to the role', 400);
        }

        $role->privileges()->detach($privilege->id);

        $role->load('privileges');

        return $this->successResponse('Privilege removed from role successfully', new RoleResource($role));
    }
}
