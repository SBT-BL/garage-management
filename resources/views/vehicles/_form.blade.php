@php
    use App\Enums\VehicleType;
@endphp

<div class="mb-3">
    <label for="vehicle_model" class="form-label">Vehicle Model <span class="text-danger">*</span></label>
    <input
        type="text"
        name="vehicle_model"
        id="vehicle_model"
        value="{{ old('vehicle_model', $vehicle?->vehicle_model ?? '') }}"
        class="form-control @error('vehicle_model') is-invalid @enderror"
        placeholder="e.g. Honda City"
        required
    >
    @error('vehicle_model')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="vehicle_number" class="form-label">Vehicle Number <span class="text-danger">*</span></label>
    <input
        type="text"
        name="vehicle_number"
        id="vehicle_number"
        value="{{ old('vehicle_number', $vehicle?->vehicle_number ?? '') }}"
        class="form-control @error('vehicle_number') is-invalid @enderror"
        placeholder="e.g. MH12AB1234"
        required
    >
    @error('vehicle_number')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="vehicle_type" class="form-label">Vehicle Type <span class="text-danger">*</span></label>
    <select
        name="vehicle_type"
        id="vehicle_type"
        class="form-select @error('vehicle_type') is-invalid @enderror"
        required
    >
        <option value="" disabled {{ old('vehicle_type', $vehicle?->vehicle_type?->value ?? '') === '' ? 'selected' : '' }}>
            Select type
        </option>
        @foreach (VehicleType::cases() as $type)
            <option
                value="{{ $type->value }}"
                @selected(old('vehicle_type', $vehicle?->vehicle_type?->value ?? '') === $type->value)
            >
                {{ $type->value }}
            </option>
        @endforeach
    </select>
    @error('vehicle_type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-0">
    <label for="notes" class="form-label">Notes</label>
    <textarea
        name="notes"
        id="notes"
        rows="3"
        class="form-control @error('notes') is-invalid @enderror"
        placeholder="Optional notes about this vehicle"
    >{{ old('notes', $vehicle?->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
