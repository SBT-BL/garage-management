<div class="mb-0">
    <label for="name" class="form-label">Service Name <span class="text-danger">*</span></label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $service->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        placeholder="e.g. Bike Wash, Oil Change, Car Wash"
        required
        maxlength="255"
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
