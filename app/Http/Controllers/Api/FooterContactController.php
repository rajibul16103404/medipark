<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FooterContact\StoreFooterContactRequest;
use App\Http\Resources\FooterContactResource;
use App\Models\FooterContact;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FooterContactController extends Controller
{
    use ApiResponse;

    /**
     * Get the footer contact (singleton - only one record exists).
     */
    public function show(): JsonResponse
    {
        $footerContact = FooterContact::first();

        if (! $footerContact) {
            return $this->errorResponse('Footer contact not found', 404);
        }

        return $this->successResponse('Footer contact retrieved successfully', new FooterContactResource($footerContact));
    }

    /**
     * Create or update footer contact (singleton - only one record allowed).
     */
    public function store(StoreFooterContactRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Get the existing footer contact or create a new one
        $footerContact = FooterContact::first();

        if ($footerContact) {
            // Update existing record
            $footerContact->update($validated);
            $message = 'Footer contact updated successfully';
            $statusCode = 200;
        } else {
            // Create new record
            $footerContact = FooterContact::create($validated);
            $message = 'Footer contact created successfully';
            $statusCode = 201;
        }

        return $this->successResponse($message, new FooterContactResource($footerContact->fresh()), $statusCode);
    }
}
