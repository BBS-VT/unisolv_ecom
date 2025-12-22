<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupplierContactController extends Controller
{
    /**
     * Store a newly created contact
     */
    public function store(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['company_id'] = auth()->user()->company_id;

            $contact = $supplier->contacts()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Contact added successfully.',
                'contact' => $contact->load('contactable')
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating supplier contact: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to add contact. Please try again.'
            ], 500);
        }
    }

    /**
     * Update the specified contact
     */
    public function update(Request $request, Supplier $supplier, Contact $contact)
    {
        // Verify contact belongs to this supplier
        if ($contact->contactable_id !== $supplier->id || $contact->contactable_type !== get_class($supplier)) {
            return response()->json([
                'success' => false,
                'message' => 'Contact does not belong to this supplier.'
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:100',
            'department' => 'nullable|string|max:100',
            'is_primary' => 'boolean',
            'is_active' => 'boolean',
        ]);

        try {
            $contact->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Contact updated successfully.',
                'contact' => $contact->fresh()->load('contactable')
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating supplier contact: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact. Please try again.'
            ], 500);
        }
    }

    /**
     * Remove the specified contact
     */
    public function destroy(Supplier $supplier, Contact $contact)
    {
        // Verify contact belongs to this supplier
        if ($contact->contactable_id !== $supplier->id || $contact->contactable_type !== get_class($supplier)) {
            return response()->json([
                'success' => false,
                'message' => 'Contact does not belong to this supplier.'
            ], 403);
        }

        try {
            $contact->delete();

            return response()->json([
                'success' => true,
                'message' => 'Contact deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting supplier contact: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete contact.'
            ], 500);
        }
    }

    /**
     * Make contact primary
     */
    public function makePrimary(Supplier $supplier, Contact $contact)
    {
        // Verify contact belongs to this supplier
        if ($contact->contactable_id !== $supplier->id || $contact->contactable_type !== get_class($supplier)) {
            return response()->json([
                'success' => false,
                'message' => 'Contact does not belong to this supplier.'
            ], 403);
        }

        try {
            $contact->makePrimary();

            return response()->json([
                'success' => true,
                'message' => 'Contact set as primary.',
                'contact' => $contact->fresh()
            ]);

        } catch (\Exception $e) {
            Log::error('Error setting primary contact: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to set primary contact.'
            ], 500);
        }
    }

    /**
     * Toggle contact active status
     */
    public function toggleActive(Supplier $supplier, Contact $contact)
    {
        // Verify contact belongs to this supplier
        if ($contact->contactable_id !== $supplier->id || $contact->contactable_type !== get_class($supplier)) {
            return response()->json([
                'success' => false,
                'message' => 'Contact does not belong to this supplier.'
            ], 403);
        }

        try {
            if ($contact->is_active) {
                $contact->deactivate();
            } else {
                $contact->activate();
            }

            return response()->json([
                'success' => true,
                'is_active' => $contact->is_active,
                'message' => 'Contact status updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling contact status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update contact status.'
            ], 500);
        }
    }
}
