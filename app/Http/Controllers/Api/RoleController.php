<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\AssignPrivilegesRequest;
use App\Http\Resources\RoleResource;
use App\Models\Privilege;
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
     * Assign privileges to a role.
     */
    public function assignPrivileges(AssignPrivilegesRequest $request, Role $role): JsonResponse
    {
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
        $role->privileges()->detach();

        $role->load('privileges');

        return response()->json([
            'message' => 'All privileges removed from role successfully',
            'role' => new RoleResource($role),
        ]);
    }
}
