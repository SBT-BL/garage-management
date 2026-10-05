@php
    use App\Enums\JobCardStatus;

    $selectedStatus = (string) old('status', $jobCard?->status->value ?? JobCardStatus::Pending->value);
    $jobCardDate = old('date', isset($jobCard) ? $jobCard->date->toDateString() : now()->toDateString());
    $displayTotal = 0;

    foreach ($serviceRows as $serviceRow) {
        if (is_numeric($serviceRow['price'] ?? null)) {
            $displayTotal += (float) $serviceRow['price'];
        }
    }
@endphp

<div class="row g-3">
    <div class="col-12 col-md-4">
        <label class="form-label" for="job_card_number">Job Card Number</label>
        <input
            type="text"
            id="job_card_number"
            class="form-control"
            value="{{ $jobCard->job_card_number ?? 'Assigned when you save' }}"
            readonly
        >
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label" for="date">Date <span class="text-danger">*</span></label>
        <input
            type="date"
            name="date"
            id="date"
            value="{{ $jobCardDate }}"
            class="form-control @error('date') is-invalid @enderror"
            required
        >
        @error('date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-4">
        <label class="form-label" for="status">Status <span class="text-danger">*</span></label>
        <select
            name="status"
            id="status"
            class="form-select @error('status') is-invalid @enderror"
            required
        >
            @foreach (JobCardStatus::cases() as $status)
                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>
                    {{ $status->value }}
                </option>
            @endforeach
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <x-select2-with-add
            id="customer_id"
            name="customer_id"
            label="Customer"
            :required="true"
            placeholder="Select a customer"
            :ajax-url="route('admin.select.customers', [], false)"
            :create-url="route('admin.customers.create', [], false)"
            create-title="Create New Customer"
            :selected-value="$selectedCustomer?->id"
            :selected-text="$selectedCustomer?->name"
        />
    </div>

    <div class="col-12 col-md-6">
        <x-select2-with-add
            id="vehicle_id"
            name="vehicle_id"
            label="Vehicle"
            :required="true"
            :disabled="$selectedCustomer === null"
            placeholder="Select a vehicle"
            :ajax-url="route('admin.select.vehicles', [], false)"
            depends-on="customer_id"
            depends-param="customer_id"
            :create-url="$selectedCustomer ? route('admin.customers.vehicles.create', $selectedCustomer, false) : ''"
            :create-url-template="route('admin.customers.vehicles.create', ['customer' => '__CUSTOMER__'], false)"
            create-title="Add Vehicle"
            :selected-value="$selectedVehicle?->id"
            :selected-text="$selectedVehicle?->optionLabel()"
        />
    </div>
</div>

<div class="mt-4">
    <h2 class="h6 text-uppercase text-muted mb-3">Services</h2>

    @error('services')
        <div class="text-danger small mb-2">{{ $message }}</div>
    @enderror

    <div data-service-rows>
        @foreach ($serviceRows as $index => $row)
            @include('job-cards.partials.service-row', ['index' => $index, 'row' => $row])
        @endforeach
    </div>

    <button type="button" class="btn btn-success w-100" data-add-service>
        <i class="bi bi-plus-lg me-1"></i> Add more service
    </button>

    <template id="job-card-service-row-template">
        @include('job-cards.partials.service-row', [
            'index' => '__INDEX__',
            'row' => ['service_id' => '', 'price' => '', 'remark' => ''],
        ])
    </template>
</div>

<div class="mt-2">
    <label class="form-label" for="remark">Remark</label>
    <textarea
        name="remark"
        id="remark"
        rows="3"
        class="form-control @error('remark') is-invalid @enderror"
        maxlength="2000"
    >{{ old('remark', $jobCard->remark ?? '') }}</textarea>
    @error('remark')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="d-flex justify-content-between align-items-center border-top mt-4 pt-3">
    <span class="text-muted">Grand Total</span>
    <span class="fs-4 fw-semibold" data-grand-total>{{ number_format($displayTotal, 2, '.', '') }}</span>
</div>
