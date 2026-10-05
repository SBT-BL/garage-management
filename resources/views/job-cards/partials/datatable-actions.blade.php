<div class="d-inline-flex gap-1 justify-content-end">
    <a
        href="{{ route('admin.job-cards.show', $jobCard) }}"
        class="btn btn-sm btn-soft-success btn-icon"
        title="View"
        aria-label="View {{ $jobCard->job_card_number }}"
    >
        <i class="bi bi-eye"></i>
    </a>
    <a
        href="{{ route('admin.job-cards.edit', $jobCard) }}"
        class="btn btn-sm btn-soft-primary btn-icon"
        title="Edit"
        aria-label="Edit {{ $jobCard->job_card_number }}"
    >
        <i class="bi bi-pencil"></i>
    </a>
    <button
        type="button"
        class="btn btn-sm btn-soft-danger btn-icon"
        title="Delete"
        aria-label="Delete {{ $jobCard->job_card_number }}"
        data-bs-toggle="modal"
        data-bs-target="#deleteJobCardModal"
        data-delete-url="{{ route('admin.job-cards.destroy', $jobCard) }}"
        data-job-card-number="{{ $jobCard->job_card_number }}"
    >
        <i class="bi bi-trash"></i>
    </button>
</div>
