<div class="d-flex align-items-center gap-2">
    <span class="avatar-initial">{{ $service->initial() }}</span>
    <div class="min-w-0">
        <a
            href="{{ route('admin.services.show', $service) }}"
            class="fw-semibold text-decoration-none text-dark text-truncate d-inline-block"
        >
            {{ $service->name }}
        </a>
    </div>
</div>
