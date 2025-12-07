<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Return a successful JSON response.
     */
    protected function successResponse(string $message, mixed $data = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Return a paginated JSON response.
     */
    protected function paginatedResponse(string $message, LengthAwarePaginator $paginator, ResourceCollection $resourceCollection): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'pagination' => [
                'per_page' => $paginator->perPage(),
                'total_count' => $paginator->total(),
                'total_page' => $paginator->lastPage(),
                'current_page' => $paginator->currentPage(),
                'current_page_count' => $paginator->count(),
                'next_page' => $paginator->hasMorePages() ? $paginator->currentPage() + 1 : null,
                'previous_page' => $paginator->currentPage() > 1 ? $paginator->currentPage() - 1 : null,
            ],
            'data' => $resourceCollection->resolve(),
        ]);
    }

    /**
     * Return an error JSON response.
     */
    protected function errorResponse(string $message, int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }
}
