@forelse ($jobCards as $jobCard)
    <article class="mobile-card">
        <div class="mobile-card-actions">
            <a
                href="{{ route('admin.job-cards.show', $jobCard) }}"
                class="mobile-card-action mobile-card-action-view"
                title="View"
                aria-label="View {{ $jobCard->customer->name }}"
            >
                <i class="bi bi-eye" aria-hidden="true"></i>
            </a>
            <a
                href="{{ route('admin.job-cards.edit', $jobCard) }}"
                class="mobile-card-action"
                title="Edit"
                aria-label="Edit {{ $jobCard->customer->name }}"
            >
                <i class="bi bi-pencil" aria-hidden="true"></i>
            </a>
            <button
                type="button"
                class="mobile-card-action mobile-card-action-danger"
                title="Delete"
                aria-label="Delete {{ $jobCard->customer->name }}"
                data-bs-toggle="modal"
                data-bs-target="#deleteJobCardModal"
                data-delete-url="{{ route('admin.job-cards.destroy', $jobCard) }}"
                data-job-card-number="{{ $jobCard->job_card_number }}"
            >
                <i class="bi bi-trash" aria-hidden="true"></i>
            </button>
        </div>

        <a href="{{ route('admin.job-cards.show', $jobCard) }}" class="mobile-card-body mobile-card-body-with-status">
            <span class="mobile-card-avatar" aria-hidden="true">
                {{ $jobCard->customer->initial() }}
            </span>
            <h3 class="mobile-card-title">{{ $jobCard->customer->name }}</h3>
            <p class="mobile-card-subtitle">{{ $jobCard->vehicle->optionLabel() }}</p>
            <p class="mobile-card-meta">{{ $jobCard->date->format('M j, Y') }}</p>
        </a>
        <div class="mobile-card-status-picker">
            @include('job-cards.partials.status-select', ['jobCard' => $jobCard])
        </div>
    </article>
@empty
    @if ($jobCards->currentPage() === 1)
        <div class="mobile-cards-empty">
            <div class="mobile-cards-empty-icon" aria-hidden="true">
                <i class="bi bi-clipboard2-check"></i>
            </div>
            <p class="mb-0 fw-semibold">No job cards found</p>
            <p class="text-muted small mb-0">Try a different search or add a new job card.</p>
        </div>
    @endif
@endforelse
