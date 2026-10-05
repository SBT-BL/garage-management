@php
    use App\Enums\JobCardStatus;
@endphp

<span class="job-card-status-picker {{ $jobCard->status->badgeClass() }}">
    <select
        class="job-card-status-select"
        data-job-card-status
        data-status-url="{{ route('admin.job-cards.status.update', $jobCard) }}"
        aria-label="Change status for {{ $jobCard->customer->name }}"
    >
        @foreach (JobCardStatus::cases() as $status)
            <option value="{{ $status->value }}" @selected($jobCard->status === $status)>
                {{ $status->value }}
            </option>
        @endforeach
    </select>
    <i class="bi bi-chevron-down" aria-hidden="true"></i>
</span>
