<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contact\CreateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    use ApiResponse;

    /**
     * List all contacts (Admin only).
     */
    public function index(): JsonResponse
    {
        $contacts = Contact::latest()->paginate(10);
        $resourceCollection = ContactResource::collection($contacts);

        return $this->paginatedResponse('Contacts retrieved successfully', $contacts, $resourceCollection);
    }

    /**
     * Show a specific contact (Admin only).
     */
    public function show(Contact $contact): JsonResponse
    {
        return $this->successResponse('Contact retrieved successfully', new ContactResource($contact));
    }

    /**
     * Create a new contact submission (Public).
     */
    public function store(CreateContactRequest $request): JsonResponse
    {
        $contact = Contact::create($request->validated());

        return $this->successResponse('Contact submitted successfully', new ContactResource($contact), 201);
    }

    /**
     * Delete a contact (Admin only).
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return $this->successResponse('Contact deleted successfully');
    }
}
