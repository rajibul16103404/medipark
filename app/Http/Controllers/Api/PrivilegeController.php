<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivilegeResource;
use App\Models\Privilege;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PrivilegeController extends Controller
{
    use ApiResponse;

    /**
     * List all privileges.
     */
    public function index(): JsonResponse
    {
        $privileges = Privilege::paginate(10);
        $resourceCollection = PrivilegeResource::collection($privileges);

        return $this->paginatedResponse('Privileges retrieved successfully', $privileges, $resourceCollection);
    }

    /**
     * Show a specific privilege.
     */
    public function show(Privilege $privilege): JsonResponse
    {
        return $this->successResponse('Privilege retrieved successfully', new PrivilegeResource($privilege));
    }
}
