<div class="d-inline-flex gap-1 justify-content-end">
    <a
        href="#"
        class="btn btn-sm btn-soft-success btn-icon"
        title="View"
        aria-label="View {{ $vehicle->vehicle_number }}"
        data-ajax-popup="true"
        data-size="md"
        data-title="Vehicle Details"
        data-url="{{ route('admin.customers.vehicles.show', [$customer, $vehicle]) }}"
    >
        <i class="bi bi-eye"></i>
    </a>
    <a
        href="#"
        class="btn btn-sm btn-soft-primary btn-icon"
        title="Edit"
        aria-label="Edit {{ $vehicle->vehicle_number }}"
        data-ajax-popup="true"
        data-size="md"
        data-title="Edit Vehicle"
        data-url="{{ route('admin.customers.vehicles.edit', [$customer, $vehicle]) }}"
    >
        <i class="bi bi-pencil"></i>
    </a>
    <button
        type="button"
        class="btn btn-sm btn-soft-danger btn-icon"
        title="Delete"
        aria-label="Delete {{ $vehicle->vehicle_number }}"
        data-bs-toggle="modal"
        data-bs-target="#deleteVehicleModal"
        data-delete-url="{{ route('admin.customers.vehicles.destroy', [$customer, $vehicle]) }}"
        data-vehicle-label="{{ $vehicle->vehicle_model }} ({{ $vehicle->vehicle_number }})"
    >
        <i class="bi bi-trash"></i>
    </button>
</div>
