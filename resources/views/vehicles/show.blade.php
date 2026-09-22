<div class="modal-body">
    <div class="mb-3">
        <div class="small text-muted mb-1">Vehicle Model</div>
        <div class="fw-medium">{{ $vehicle->vehicle_model }}</div>
    </div>

    <div class="mb-3">
        <div class="small text-muted mb-1">Vehicle Number</div>
        <div class="fw-medium">{{ $vehicle->vehicle_number }}</div>
    </div>

    <div class="mb-3">
        <div class="small text-muted mb-1">Vehicle Type</div>
        <div class="fw-medium">{{ $vehicle->vehicle_type->value }}</div>
    </div>

    <div class="mb-3">
        <div class="small text-muted mb-1">Notes</div>
        <div class="fw-medium">{{ $vehicle->notes ?: 'No notes' }}</div>
    </div>

    <div>
        <div class="small text-muted mb-1">Added</div>
        <div class="fw-medium">{{ $vehicle->created_at->format('M j, Y g:i A') }}</div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
</div>
