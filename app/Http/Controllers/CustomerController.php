<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    private const PER_PAGE_OPTIONS = [10, 15, 25, 50];

    /**
     * Display a listing of customers.
     */
    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $perPage = (int) $request->input('per_page', 10);

        if (! in_array($perPage, self::PER_PAGE_OPTIONS, true)) {
            $perPage = 10;
        }

        $customers = Customer::query()
            ->search($search)
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('customers.index', [
            'customers' => $customers,
            'search' => $search,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
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
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::query()->create($request->validated());

        return redirect()
            ->route('admin.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     */
    public function show(Customer $customer): View
    {
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
