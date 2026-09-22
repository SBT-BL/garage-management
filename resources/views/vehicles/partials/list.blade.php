@if ($customer->vehicles->isEmpty())
    <div class="empty-state py-4">
        <div class="empty-state-icon">
            <i class="bi bi-truck"></i>
        </div>
        <h3 class="h6 mb-2">No vehicles added yet</h3>
        <p class="text-muted mb-3 small">
            Add the first vehicle for this customer to get started.
        </p>
        <a
            href="#"
            class="btn btn-sm btn-success"
            data-ajax-popup="true"
            data-size="md"
            data-title="Add Vehicle"
            data-url="{{ route('admin.customers.vehicles.create', $customer) }}"
        >
            <i class="bi bi-plus-lg me-1"></i> Add Vehicle
        </a>
    </div>
@else
    {{-- Desktop / tablet table --}}
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-customers w-100 mb-0">
            <thead>
                <tr>
                    <th scope="col">Model</th>
                    <th scope="col">Number</th>
                    <th scope="col">Type</th>
                    <th scope="col">Notes</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer->vehicles as $vehicle)
                    <tr>
                        <td class="fw-medium">{{ $vehicle->vehicle_model }}</td>
                        <td>{{ $vehicle->vehicle_number }}</td>
                        <td>
                            <span class="badge text-bg-light border">{{ $vehicle->vehicle_type->value }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ $vehicle->notes ? \Illuminate\Support\Str::limit($vehicle->notes, 40) : '—' }}
                        </td>
                        <td class="text-end">
                            @include('vehicles.partials.actions', ['customer' => $customer, 'vehicle' => $vehicle])
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="d-md-none d-flex flex-column gap-3">
        @foreach ($customer->vehicles as $vehicle)
            <div class="border rounded p-3">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <div class="fw-medium">{{ $vehicle->vehicle_model }}</div>
                        <div class="small text-muted">{{ $vehicle->vehicle_number }}</div>
                    </div>
                    <span class="badge text-bg-light border">{{ $vehicle->vehicle_type->value }}</span>
                </div>
                @if ($vehicle->notes)
                    <p class="small text-muted mb-3">{{ \Illuminate\Support\Str::limit($vehicle->notes, 80) }}</p>
                @endif
                <div class="d-flex justify-content-end">
                    @include('vehicles.partials.actions', ['customer' => $customer, 'vehicle' => $vehicle])
                </div>
            </div>
        @endforeach
    </div>
@endif
