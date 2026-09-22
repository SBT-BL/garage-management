<div class="d-inline-flex gap-1 justify-content-end">
    <a
        href="{{ route('admin.customers.show', $customer) }}"
        class="btn btn-sm btn-soft-success btn-icon"
        title="View"
        aria-label="View {{ $customer->name }}"
    >
        <i class="bi bi-eye"></i>
    </a>
    <a
        href="#"
        class="btn btn-sm btn-soft-primary btn-icon"
        title="Edit"
        aria-label="Edit {{ $customer->name }}"
        data-ajax-popup="true"
        data-size="md"
        data-title="Edit Customer"
        data-url="{{ route('admin.customers.edit', $customer) }}"
    >
        <i class="bi bi-pencil"></i>
    </a>
    <button
        type="button"
        class="btn btn-sm btn-soft-danger btn-icon"
        title="Delete"
        aria-label="Delete {{ $customer->name }}"
        data-bs-toggle="modal"
        data-bs-target="#deleteCustomerModal"
        data-delete-url="{{ route('admin.customers.destroy', $customer) }}"
        data-customer-name="{{ $customer->name }}"
    >
        <i class="bi bi-trash"></i>
    </button>
</div>
