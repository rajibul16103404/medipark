<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\CreateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    /**
     * List all contacts (Admin only).
     */
    public function index(): JsonResponse
    {
        $contacts = Contact::latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Contacts retrieved successfully',
            'pagination' => [
                'per_page' => $contacts->perPage(),
                'total_count' => $contacts->total(),
                'total_page' => $contacts->lastPage(),
                'current_page' => $contacts->currentPage(),
                'current_page_count' => $contacts->count(),
                'next_page' => $contacts->hasMorePages() ? $contacts->currentPage() + 1 : null,
                'previous_page' => $contacts->currentPage() > 1 ? $contacts->currentPage() - 1 : null,
            ],
            'data' => ContactResource::collection($contacts)->resolve(),
        ]);
    }

    /**
     * Show a specific contact (Admin only).
     */
    public function show(Contact $contact): JsonResponse
    {
        return response()->json([
            'contact' => new ContactResource($contact),
        ]);
    }

    /**
     * Create a new contact submission (Public).
     */
    public function store(CreateContactRequest $request): JsonResponse
    {
        $contact = Contact::create($request->validated());

        return response()->json([
            'message' => 'Contact submitted successfully',
            'contact' => new ContactResource($contact),
        ], 201);
    }

    /**
     * Delete a contact (Admin only).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully',
        ]);
    }
}
