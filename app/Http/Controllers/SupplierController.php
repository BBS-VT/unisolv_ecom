<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class SupplierController extends Controller
{
    /**
     * Display listing of suppliers
     */
    public function index(Request $request)
    {
        //abort_if(Gate::denies('supplier_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user = $request->user();
        $currentCompany = $user->currentCompany();

        //$query = Supplier::with(['country', 'currency', 'primaryContact'])
        $query = Supplier::with(['country', 'currency'])
                    //->forCompany();
                    ->where('company_id', $currentCompany->id);

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            } elseif ($request->status === 'on_hold') {
                $query->on_hold();
            }
        }

        $suppliers = $query->orderBy('SupplierName')->paginate(25);

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $currencies = Currency::orderBy('code')->get();

        return view('suppliers.create', compact('countries', 'currencies'));
    }

    /**
     * Store a newly created supplier in storage
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $currentCompany = $user->currentCompany();

        $validated = $request->validate([
            'SupplierName' => 'required|string|max:255',
            'acc_main' => 'nullable|string|max:11',
            'acc_sub' => 'nullable|string|max:3',
            'VatNr' => 'nullable|string|max:50',
            'tax_reference' => 'nullable|string|max:50',
            'PhoneNumber' => 'nullable|string|max:20',
            'FaxNumber' => 'nullable|string|max:20',
            'WebsiteURL' => 'nullable|url|max:255',
            'GeneralEmailAddress' => 'nullable|email|max:255',
            'CreditLimit' => 'nullable|numeric|min:0',
            'PaymentDays' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string|max:50',
            'StandardDiscountPercentage' => 'nullable|numeric|min:0|max:100',
            'IsOnCreditHold' => 'boolean',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'CountryID' => 'nullable|exists:countries,id',
            'AccountOpenedDate' => 'nullable|date',
            'Status' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['company_id'] = $currentCompany->id;
            $validated['LastEditedBy'] = auth()->id();

            $supplier = Supplier::create($validated);

            // Create addresses if provided
            if ($request->filled('delivery_address_line1')) {
                $supplier->updateAddress('delivery', [
                    'name'  => $request->SupplierName,
                    'address_1' => $request->delivery_address_line1,
                    'address_2' => $request->delivery_address_line2,
                    'city' => $request->delivery_city,
                    'zip' => $request->delivery_postal_code,
                    'province' => $request->delivery_province,
                    'country_id' => $request->CountryID,
                ]);
            }

            if ($request->filled('postal_address_line1')) {
                $supplier->updateAddress('postal', [
                    'name'  => $request->SupplierName,
                    'address_1' => $request->postal_address_line1,
                    'address_2' => $request->postal_address_line2,
                    'city' => $request->postal_city,
                    'zip' => $request->postal_postal_code,
                    'province' => $request->postal_province,
                    'country_id' => $request->CountryID,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('success', 'Supplier created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating supplier: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to create supplier. Please try again.');
        }
    }

    /**
     * Display the specified supplier
     */
    public function show(Supplier $supplier)
    {
        $supplier->load([
            'country',
            'currency',
            'contacts',
            'primaryContact',
            /*'purchaseOrders' => function($query) {
                $query->latest()->limit(10);
            }*/
        ]);

        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit(Supplier $supplier)
    {
        $countries = Country::orderBy('name')->get();
        $currencies = Currency::orderBy('code')->get();

        return view('suppliers.edit', compact('supplier', 'countries', 'currencies'));
    }

    /**
     * Update the specified supplier in storage
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'SupplierName' => 'required|string|max:255',
            'acc_main' => 'nullable|string|max:11',
            'acc_sub' => 'nullable|string|max:3',
            'VatNr' => 'nullable|string|max:50',
            'tax_reference' => 'nullable|string|max:50',
            'PhoneNumber' => 'nullable|string|max:20',
            'FaxNumber' => 'nullable|string|max:20',
            'WebsiteURL' => 'nullable|url|max:255',
            'GeneralEmailAddress' => 'nullable|email|max:255',
            'CreditLimit' => 'nullable|numeric|min:0',
            'PaymentDays' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string|max:50',
            'StandardDiscountPercentage' => 'nullable|numeric|min:0|max:100',
            'IsOnCreditHold' => 'boolean',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'bank_branch' => 'nullable|string|max:255',
            'lead_time_days' => 'nullable|integer|min:0',
            'minimum_order_value' => 'nullable|numeric|min:0',
            'currency_id' => 'nullable|exists:currencies,id',
            'CountryID' => 'nullable|exists:countries,id',
            'AccountOpenedDate' => 'nullable|date',
            'Status' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $validated['LastEditedBy'] = auth()->id();
            $supplier->update($validated);

            // Update addresses
            if ($request->filled('delivery_address_line1')) {
                $supplier->updateAddress('delivery', [
                    'address_line1' => $request->delivery_address_line1,
                    'address_line2' => $request->delivery_address_line2,
                    'city' => $request->delivery_city,
                    'postal_code' => $request->delivery_postal_code,
                    'province' => $request->delivery_province,
                    'country_id' => $request->CountryID,
                ]);
            }

            if ($request->filled('postal_address_line1')) {
                $supplier->updateAddress('postal', [
                    'address_line1' => $request->postal_address_line1,
                    'address_line2' => $request->postal_address_line2,
                    'city' => $request->postal_city,
                    'postal_code' => $request->postal_postal_code,
                    'province' => $request->postal_province,
                    'country_id' => $request->CountryID,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('suppliers.show', $supplier)
                ->with('success', 'Supplier updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating supplier: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update supplier. Please try again.');
        }
    }

    /**
     * Remove the specified supplier from storage
     */
    public function destroy(Supplier $supplier)
    {
        try {
            // Check if supplier has purchase orders
            if ($supplier->purchaseOrders()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete supplier with existing purchase orders.'
                ], 422);
            }

            $supplier->delete();

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting supplier: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete supplier.'
            ], 500);
        }
    }

    /**
     * Toggle supplier status
     */
    public function toggleStatus(Supplier $supplier)
    {
        try {
            $supplier->Status = !$supplier->Status;
            $supplier->LastEditedBy = auth()->id();
            $supplier->save();

            return response()->json([
                'success' => true,
                'status' => $supplier->Status,
                'message' => 'Supplier status updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling supplier status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update supplier status.'
            ], 500);
        }
    }

    /**
     * Toggle credit hold status
     */
    public function toggleCreditHold(Supplier $supplier)
    {
        try {
            $supplier->IsOnCreditHold = !$supplier->IsOnCreditHold;
            $supplier->LastEditedBy = auth()->id();
            $supplier->save();

            return response()->json([
                'success' => true,
                'is_on_hold' => $supplier->IsOnCreditHold,
                'message' => 'Credit hold status updated successfully.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling credit hold: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update credit hold status.'
            ], 500);
        }
    }
}
