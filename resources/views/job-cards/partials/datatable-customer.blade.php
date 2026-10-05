<div class="d-flex align-items-center gap-2">
    <span class="avatar-initial">{{ $jobCard->customer?->initial() ?? '—' }}</span>
    <div class="min-w-0">
        <a
            href="{{ route('admin.job-cards.show', $jobCard) }}"
            class="fw-semibold text-decoration-none text-dark text-truncate d-inline-block"
        >
            {{ $jobCard->customer?->name ?? '—' }}
        </a>
    </div>
</div>
