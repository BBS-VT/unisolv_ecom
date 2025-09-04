<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LocationController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('settings_access'), 403);

        $locations = Location::orderBy('SortOrder')
            ->orderBy('LocationCode')
            ->get();

        return view('admin.settings.product.locations.index', compact('locations'));
    }

    public function create()
    {
        abort_if(Gate::denies('settings_create'), 403);

        // Get next location code
        $lastLocation = Location::orderByDesc('LocationCode')->first();
        $nextCode = $lastLocation ? sprintf('%04d', intval($lastLocation->LocationCode) + 1) : '0001';

        return view('admin.settings.product.locations.create', compact('nextCode'));
    }

    public function store(StoreLocationRequest $request)
    {
        abort_if(Gate::denies('settings_create'), 403);

        $validated = $request->validated();

        // Set LastEditedBy
        $validated['LastEditedBy'] = auth()->user()->name ?? 'System';

        // Handle default location logic
        if ($validated['IsDefault']) {
            Location::where('IsDefault', true)->update(['IsDefault' => false]);
        }

        $location = Location::create($validated);

        return redirect()
            ->route('admin.settings.product', ['#locations'])
            ->with('success', __('messages.location_created_successfully', ['name' => $location->LocationName]));
    }

    public function show(Location $location)
    {
        abort_if(Gate::denies('settings_show'), 403);

        // Load stock holdings count for this location
        $location->load('stockHoldings');
        $stockCount = $location->stockHoldings->count();
        $totalQuantity = $location->stockHoldings->sum('QuantityOnHand');

        return view('admin.settings.product.locations.show', compact('location', 'stockCount', 'totalQuantity'));
    }

    public function edit(Location $location)
    {
        abort_if(Gate::denies('settings_edit'), 403);

        return view('admin.settings.product.locations.edit', compact('location'));
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        abort_if(Gate::denies('settings_edit'), 403);

        $validated = $request->validated();

        // Set LastEditedBy
        $validated['LastEditedBy'] = auth()->user()->name ?? 'System';

        // Handle default location logic
        if ($validated['IsDefault'] && !$location->IsDefault) {
            Location::where('LocationCode', '!=', $location->LocationCode)
                ->where('IsDefault', true)
                ->update(['IsDefault' => false]);
        }

        $location->update($validated);

        return redirect()
            ->route('admin.settings.product', ['#locations'])
            ->with('success', __('messages.location_updated_successfully', ['name' => $location->LocationName]));
    }

    public function destroy(Location $location)
    {
        abort_if(Gate::denies('settings_delete'), 403);

        // Prevent deleting default location
        if ($location->IsDefault) {
            return redirect()
                ->back()
                ->with('error', __('messages.cannot_delete_default_location'));
        }

        // Check if location has stock holdings
        $stockCount = $location->stockHoldings()->count();
        if ($stockCount > 0) {
            return redirect()
                ->back()
                ->with('error', __('messages.cannot_delete_location_with_stock', ['count' => $stockCount]));
        }

        $locationName = $location->LocationName;
        $location->delete();

        return redirect()
            ->route('admin.settings.product', ['#locations'])
            ->with('success', __('messages.location_deleted_successfully', ['name' => $locationName]));
    }

    /**
     * Toggle location active status
     */
    public function toggleStatus(Location $location)
    {
        abort_if(Gate::denies('settings_edit'), 403);

        // Prevent deactivating default location
        if ($location->IsDefault && $location->IsActive) {
            return response()->json([
                'success' => false,
                'message' => __('messages.cannot_deactivate_default_location')
            ]);
        }

        $location->update([
            'IsActive' => !$location->IsActive,
            'LastEditedBy' => auth()->user()->name ?? 'System'
        ]);

        return response()->json([
            'success' => true,
            'isActive' => $location->IsActive,
            'message' => __('messages.location_status_updated')
        ]);
    }

    /**
     * Generate next location code
     */
    public function generateCode()
    {
        abort_if(Gate::denies('settings_create'), 403);

        $lastLocation = Location::orderByDesc('LocationCode')->first();
        $nextCode = $lastLocation ? sprintf('%04d', intval($lastLocation->LocationCode) + 1) : '0001';

        return response()->json(['code' => $nextCode]);
    }
}
