@php
    $selectedServiceId = (string) ($row['service_id'] ?? '');
    $selectedServiceText = $selectedServiceId !== ''
        ? ($serviceNames[(int) $selectedServiceId] ?? null)
        : null;
@endphp

<div class="job-card-service-row border rounded p-3 mb-3" data-service-row>
    <div class="d-flex justify-content-end mb-2">
        <button
            type="button"
            class="btn btn-sm btn-soft-danger btn-icon"
            data-remove-service
            title="Remove service"
            aria-label="Remove service"
        >
            <i class="bi bi-trash"></i>
        </button>
    </div>
    <div class="row g-3 align-items-start">
        <div class="col-12 col-lg-4">
            <x-select2-with-add
                id="service-{{ $index }}"
                name="services[{{ $index }}][service_id]"
                label="Service"
                :required="true"
                placeholder="Select a service"
                :ajax-url="route('admin.select.services', [], false)"
                :create-url="route('admin.services.create', [], false)"
                create-title="Create New Service"
                :selected-value="$selectedServiceId !== '' ? $selectedServiceId : null"
                :selected-text="$selectedServiceText"
                error-key="services.{{ $index }}.service_id"
                data-service-select
            />
        </div>

        <div class="col-12 col-sm-4 col-lg-3">
            <label class="form-label" for="price-{{ $index }}">Price <span class="text-danger">*</span></label>
            <input
                type="number"
                name="services[{{ $index }}][price]"
                id="price-{{ $index }}"
                value="{{ $row['price'] ?? '' }}"
                class="form-control @error('services.'.$index.'.price') is-invalid @enderror"
                data-service-price
                min="0"
                step="0.01"
                inputmode="decimal"
                required
            >
            @error('services.'.$index.'.price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-sm-8 col-lg-5">
            <label class="form-label" for="service-remark-{{ $index }}">Remark</label>
            <input
                type="text"
                name="services[{{ $index }}][remark]"
                id="service-remark-{{ $index }}"
                value="{{ $row['remark'] ?? '' }}"
                class="form-control @error('services.'.$index.'.remark') is-invalid @enderror"
                maxlength="1000"
            >
            @error('services.'.$index.'.remark')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
