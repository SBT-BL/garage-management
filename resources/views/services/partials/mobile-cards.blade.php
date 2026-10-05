@forelse ($services as $service)
    <article class="mobile-card">
        <div class="mobile-card-actions">
            <a
                href="{{ route('admin.services.show', $service) }}"
                class="mobile-card-action mobile-card-action-view"
                title="View"
                aria-label="View {{ $service->name }}"
            >
                <i class="bi bi-eye" aria-hidden="true"></i>
            </a>
            <a
                href="#"
                class="mobile-card-action"
                title="Edit"
                aria-label="Edit {{ $service->name }}"
                data-ajax-popup="true"
                data-size="md"
                data-title="Edit Service"
                data-url="{{ route('admin.services.edit', $service) }}"
            >
                <i class="bi bi-pencil" aria-hidden="true"></i>
            </a>
            <button
                type="button"
                class="mobile-card-action mobile-card-action-danger"
                title="Delete"
                aria-label="Delete {{ $service->name }}"
                data-bs-toggle="modal"
                data-bs-target="#deleteServiceModal"
                data-delete-url="{{ route('admin.services.destroy', $service) }}"
                data-service-name="{{ $service->name }}"
            >
                <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
        </div>

        <a href="{{ route('admin.services.show', $service) }}" class="mobile-card-body">
            <span class="mobile-card-avatar" aria-hidden="true">
                {{ $service->initial() }}
            </span>
            <h3 class="mobile-card-title">{{ $service->name }}</h3>
            <p class="mobile-card-meta">
                Added {{ $service->created_at->format('M j, Y') }}
            </p>
        </a>
    </article>
@empty
    @if ($services->currentPage() === 1)
        <div class="mobile-cards-empty">
            <div class="mobile-cards-empty-icon" aria-hidden="true">
                <i class="bi bi-wrench-adjustable"></i>
            </div>
            <p class="mb-0 fw-semibold">No services found</p>
            <p class="text-muted small mb-0">Try a different search or add a new service.</p>
        </div>
    @endif
@endforelse
