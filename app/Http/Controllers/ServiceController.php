<?php

namespace App\Http\Controllers;

use App\DataTables\ServicesDataTable;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Display a listing of services (Yajra DataTables).
     */
    public function index(ServicesDataTable $dataTable): View|JsonResponse
    {
        return $dataTable->render('services.index');
    }

    /**
     * Return a paginated mobile card feed for infinite scroll.
     */
    public function cards(Request $request): JsonResponse
    {
        $services = Service::query()
            ->search($request->string('q')->toString())
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'html' => view('services.partials.mobile-cards', [
                'services' => $services,
            ])->render(),
            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'has_more' => $services->hasMorePages(),
                'total' => $services->total(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create(): View
    {
        return view('services.create');
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request): RedirectResponse|JsonResponse
    {
        $service = Service::query()->create($request->validated());

        if ($request->expectsJson()) {
            return response()->json($service->selectOption());
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service created successfully.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()
            ->route('admin.services.show', $service)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service): RedirectResponse
    {
        try {
            $service->delete();
        } catch (QueryException) {
            return redirect()
                ->route('admin.services.show', $service)
                ->with('error', 'This service cannot be deleted because related records still exist.');
        }

        return redirect()
            ->route('admin.services.index')
            ->with('success', 'Service deleted successfully.');
    }
}
