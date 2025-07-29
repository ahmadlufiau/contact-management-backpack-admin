<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Display a listing of the contacts.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Optimize memory usage by selecting only needed fields
            $query = Contact::select([
                'id', 'first_name', 'last_name', 'email', 
                'phone', 'company', 'address', 'birth_date', 
                'notes', 'created_at', 'updated_at'
            ]);

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('company', 'like', "%{$search}%");
                });
            }

            // Filter by company
            if ($request->has('company') && !empty($request->company)) {
                $query->where('company', 'like', "%{$request->company}%");
            }

            // Sort functionality
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination with smaller default page size
            $perPage = min($request->get('per_page', 10), 50); // Limit max per page
            $contacts = $query->paginate($perPage);

            // Use chunked collection to reduce memory usage
            $data = ContactResource::collection($contacts->getCollection());

            return response()->json([
                'success' => true,
                'message' => 'Contacts retrieved successfully',
                'data' => $data,
                'meta' => [
                    'current_page' => $contacts->currentPage(),
                    'last_page' => $contacts->lastPage(),
                    'per_page' => $contacts->perPage(),
                    'total' => $contacts->total(),
                ],
            ], 200); // OK
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contacts',
                'error' => 'Internal server error',
            ], 500); // Internal Server Error
        }
    }

    /**
     * Store a newly created contact in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:contacts,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'birth_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ], [
            'first_name.required' => 'First name is required.',
            'first_name.max' => 'First name cannot exceed 255 characters.',
            'last_name.required' => 'Last name is required.',
            'last_name.max' => 'Last name cannot exceed 255 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'phone.max' => 'Phone number cannot exceed 20 characters.',
            'company.max' => 'Company name cannot exceed 255 characters.',
            'address.max' => 'Address cannot exceed 500 characters.',
            'birth_date.date' => 'Please enter a valid date.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $contact = Contact::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contact created successfully',
                'data' => new ContactResource($contact),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create contact',
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Display the specified contact.
     */
    public function show(Contact $contact): JsonResponse
    {
        try {
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found',
                    'error' => 'Resource not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Contact retrieved successfully',
                'data' => new ContactResource($contact),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
                'error' => 'Resource not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve contact',
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Update the specified contact in storage.
     */
    public function update(Request $request, Contact $contact): JsonResponse
    {
        try {
            // Check if contact exists
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found',
                    'error' => 'Resource not found',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:contacts,email,' . $contact->id,
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'address' => 'nullable|string|max:500',
                'birth_date' => 'nullable|date',
                'notes' => 'nullable|string|max:1000',
            ], [
                'first_name.required' => 'First name is required.',
                'first_name.max' => 'First name cannot exceed 255 characters.',
                'last_name.required' => 'Last name is required.',
                'last_name.max' => 'Last name cannot exceed 255 characters.',
                'email.required' => 'Email is required.',
                'email.email' => 'Please enter a valid email address.',
                'email.unique' => 'This email address is already in use.',
                'phone.max' => 'Phone number cannot exceed 20 characters.',
                'company.max' => 'Company name cannot exceed 255 characters.',
                'address.max' => 'Address cannot exceed 500 characters.',
                'birth_date.date' => 'Please enter a valid date.',
                'notes.max' => 'Notes cannot exceed 1000 characters.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $contact->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully',
                'data' => new ContactResource($contact),
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
                'error' => 'Resource not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact',
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Remove the specified contact from storage.
     */
    public function destroy(Contact $contact): JsonResponse
    {
        try {
            // Check if contact exists
            if (!$contact) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contact not found',
                    'error' => 'Resource not found',
                ], 404);
            }

            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully',
                'data' => null,
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found',
                'error' => 'Resource not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact',
                'error' => 'Internal server error',
            ], 500);
        }
    }
}
