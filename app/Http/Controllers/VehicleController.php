<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Show the form for creating a new vehicle.
     */
    public function create(Customer $customer): View
    {
        return view('vehicles.create', compact('customer'));
    }

    /**
     * Store a newly created vehicle for the customer.
     */
    public function store(StoreVehicleRequest $request, Customer $customer): RedirectResponse
    {
        $customer->vehicles()->create($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Vehicle added successfully.');
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Customer $customer, Vehicle $vehicle): View
    {
        return view('vehicles.show', compact('customer', 'vehicle'));
    }

    /**
     * Show the form for editing the specified vehicle.
     */
    public function edit(Customer $customer, Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('customer', 'vehicle'));
    }

    /**
     * Update the specified vehicle.
     */
    public function update(UpdateVehicleRequest $request, Customer $customer, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->update($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Vehicle updated successfully.');
    }

    /**
     * Remove the specified vehicle.
     */
    public function destroy(Customer $customer, Vehicle $vehicle): RedirectResponse
    {
        $vehicle->delete();

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Vehicle deleted successfully.');
    }
}
