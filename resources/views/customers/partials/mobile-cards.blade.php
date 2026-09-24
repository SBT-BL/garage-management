@forelse ($customers as $customer)
    <article class="mobile-card">
        <div class="mobile-card-actions">
            <a
                href="{{ route('admin.customers.show', $customer) }}"
                class="mobile-card-action mobile-card-action-view"
                title="View"
                aria-label="View {{ $customer->name }}"
            >
                <i class="bi bi-eye" aria-hidden="true"></i>
            </a>
            <a
                href="#"
                class="mobile-card-action"
                title="Edit"
                aria-label="Edit {{ $customer->name }}"
                data-ajax-popup="true"
                data-size="md"
                data-title="Edit Customer"
                data-url="{{ route('admin.customers.edit', $customer) }}"
            >
                <i class="bi bi-pencil" aria-hidden="true"></i>
            </a>
            <button
                type="button"
                class="mobile-card-action mobile-card-action-danger"
                title="Delete"
                aria-label="Delete {{ $customer->name }}"
                data-bs-toggle="modal"
                data-bs-target="#deleteCustomerModal"
                data-delete-url="{{ route('admin.customers.destroy', $customer) }}"
                data-customer-name="{{ $customer->name }}"
            >
                <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
        </div>

        <a href="{{ route('admin.customers.show', $customer) }}" class="mobile-card-body">
            <span class="mobile-card-avatar" aria-hidden="true">
                {{ $customer->initial() }}
            </span>
            <h3 class="mobile-card-title">{{ $customer->name }}</h3>
            <p class="mobile-card-subtitle">
                <i class="bi bi-whatsapp" aria-hidden="true"></i>
                {{ $customer->whatsapp_number }}
            </p>
            <p class="mobile-card-meta">
                {{ $customer->vehicles_count }}
                {{ Str::plural('vehicle', $customer->vehicles_count) }}
            </p>
        </a>
    </article>
@empty
    @if ($customers->currentPage() === 1)
        <div class="mobile-cards-empty">
            <div class="mobile-cards-empty-icon" aria-hidden="true">
                <i class="bi bi-people"></i>
            </div>
            <p class="mb-0 fw-semibold">No customers found</p>
            <p class="text-muted small mb-0">Try a different search or add a new customer.</p>
        </div>
    @endif
@endforelse
