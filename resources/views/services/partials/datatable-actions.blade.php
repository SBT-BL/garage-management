<div class="d-inline-flex gap-1 justify-content-end">
    <a
        href="{{ route('admin.services.show', $service) }}"
        class="btn btn-sm btn-soft-success btn-icon"
        title="View"
        aria-label="View {{ $service->name }}"
    >
        <i class="bi bi-eye"></i>
    </a>
    <a
        href="#"
        class="btn btn-sm btn-soft-primary btn-icon"
        title="Edit"
        aria-label="Edit {{ $service->name }}"
        data-ajax-popup="true"
        data-size="md"
        data-title="Edit Service"
        data-url="{{ route('admin.services.edit', $service) }}"
    >
        <i class="bi bi-pencil"></i>
    </a>
    <button
        type="button"
        class="btn btn-sm btn-soft-danger btn-icon"
        title="Delete"
        aria-label="Delete {{ $service->name }}"
        data-bs-toggle="modal"
        data-bs-target="#deleteServiceModal"
        data-delete-url="{{ route('admin.services.destroy', $service) }}"
        data-service-name="{{ $service->name }}"
    >
        <i class="bi bi-trash"></i>
    </button>
</div>
