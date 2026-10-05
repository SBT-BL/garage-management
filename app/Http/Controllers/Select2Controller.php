<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Select2Controller extends Controller
{
    private const RESULT_LIMIT = 20;

    /**
     * Customers for a Select2 dropdown.
     */
    public function customers(Request $request): JsonResponse
    {
        $this->abortUnlessAjax($request);

        $customers = Customer::query()
            ->search($request->input('search.value'))
            ->orderBy('name')
            ->limit(self::RESULT_LIMIT)
            ->get();

        return response()->json(
            $customers->map(fn (Customer $customer): array => $customer->selectOption())->values()
        );
    }

    /**
     * Vehicles for the selected customer.
     */
    public function vehicles(Request $request): JsonResponse
    {
        $this->abortUnlessAjax($request);

        $customerId = $request->integer('customer_id');

        if ($customerId === 0) {
            return response()->json([]);
        }

        $vehicles = Vehicle::query()
            ->where('customer_id', $customerId)
            ->search($request->input('search.value'))
            ->orderBy('vehicle_number')
            ->limit(self::RESULT_LIMIT)
            ->get();

        return response()->json(
            $vehicles->map(fn (Vehicle $vehicle): array => $vehicle->selectOption())->values()
        );
    }

    /**
     * Services for a Select2 dropdown.
     */
    public function services(Request $request): JsonResponse
    {
        $this->abortUnlessAjax($request);

        $services = Service::query()
            ->search($request->input('search.value'))
            ->orderBy('name')
            ->limit(self::RESULT_LIMIT)
            ->get();

        return response()->json(
            $services->map(fn (Service $service): array => $service->selectOption())->values()
        );
    }

    /**
     * Select2 requests are AJAX lookups, not full page loads.
     */
    private function abortUnlessAjax(Request $request): void
    {
        abort_unless($request->ajax(), 403);
    }
}
