<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivilegeResource;
use App\Models\Privilege;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PrivilegeController extends Controller
{
    /**
     * List all privileges.
     */
    public function index(): AnonymousResourceCollection
    {
        $privileges = Privilege::all();

        return PrivilegeResource::collection($privileges);
    }

    /**
     * Show a specific privilege.
     */
    public function show(Privilege $privilege): JsonResponse
    {
        return response()->json([
            'privilege' => new PrivilegeResource($privilege),
        ]);
    }
}
