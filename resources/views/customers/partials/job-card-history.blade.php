@if ($customer->jobCards->isEmpty())
    <div class="empty-state py-4">
        <div class="empty-state-icon">
            <i class="bi bi-clipboard2-check"></i>
        </div>
        <h3 class="h6 mb-2">No job cards yet</h3>
        <p class="text-muted mb-0 small">
            Job cards created for this customer will appear here.
        </p>
    </div>
@else
    {{-- Desktop / tablet table --}}
    <div class="d-none d-md-block table-responsive">
        <table class="table table-hover table-customers w-100 mb-0">
            <thead>
                <tr>
                    <th scope="col">Job Card</th>
                    <th scope="col">Date</th>
                    <th scope="col">Vehicle</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end">Total</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customer->jobCards as $jobCard)
                    <tr>
                        <td class="fw-medium">
                            <a href="{{ route('admin.job-cards.show', $jobCard) }}" class="text-decoration-none">
                                {{ $jobCard->job_card_number }}
                            </a>
                        </td>
                        <td class="text-nowrap">{{ $jobCard->date->format('M j, Y') }}</td>
                        <td>{{ $jobCard->vehicle->optionLabel() }}</td>
                        <td>
                            @include('job-cards.partials.status-badge', ['status' => $jobCard->status])
                        </td>
                        <td class="text-end text-nowrap fw-medium">{{ number_format((float) $jobCard->grand_total, 2) }}</td>
                        <td class="text-end">
                            <a
                                href="{{ route('admin.job-cards.show', $jobCard) }}"
                                class="btn btn-sm btn-soft-success btn-icon"
                                title="View"
                                aria-label="View {{ $jobCard->job_card_number }}"
                            >
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="d-md-none d-flex flex-column gap-3">
        @foreach ($customer->jobCards as $jobCard)
            <a href="{{ route('admin.job-cards.show', $jobCard) }}" class="border rounded p-3 text-decoration-none text-body">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                    <div>
                        <div class="fw-medium">{{ $jobCard->job_card_number }}</div>
                        <div class="small text-muted">{{ $jobCard->vehicle->optionLabel() }}</div>
                    </div>
                    @include('job-cards.partials.status-badge', ['status' => $jobCard->status])
                </div>
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <span class="small text-muted">{{ $jobCard->date->format('M j, Y') }}</span>
                    <span class="fw-semibold">{{ number_format((float) $jobCard->grand_total, 2) }}</span>
                </div>
            </a>
        @endforeach
    </div>
@endif
