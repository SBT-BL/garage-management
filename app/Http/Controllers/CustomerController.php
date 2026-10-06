<?php

namespace App\Http\Controllers;

use App\DataTables\CustomersDataTable;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers (Yajra DataTables).
     */
    public function index(CustomersDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('customers.index');
    }

    /**
     * Return a paginated mobile card feed for infinite scroll.
     */
    public function cards(Request $request): JsonResponse
    {
        $customers = Customer::query()
            ->withCount('vehicles')
            ->search($request->string('q')->toString())
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'html' => view('customers.partials.mobile-cards', [
                'customers' => $customers,
            ])->render(),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'has_more' => $customers->hasMorePages(),
                'total' => $customers->total(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create(): View
    {
        return view('customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse|JsonResponse
    {
        $customer = Customer::query()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($customer->selectOption());
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
        $customer->load([
            'vehicles' => fn ($query) => $query->latest(),
            'jobCards' => fn ($query) => $query
                ->with('vehicle:id,vehicle_number,vehicle_model')
                ->latest('date')
                ->latest('id'),
        ]);

        return view('customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        try {
            $customer->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.customers.show', $customer)
                ->with('error', 'This customer cannot be deleted because related records still exist.');
        }

        return redirect()
            ->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
