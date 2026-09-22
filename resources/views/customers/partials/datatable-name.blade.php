<div class="d-flex align-items-center gap-2">
    <span class="avatar-initial">{{ $customer->initial() }}</span>
    <div class="min-w-0">
        <a
            href="{{ route('admin.customers.show', $customer) }}"
            class="fw-semibold text-decoration-none text-dark text-truncate d-inline-block"
        >
            {{ $customer->name }}
        </a>
    </div>
</div>
