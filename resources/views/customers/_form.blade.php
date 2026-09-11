<div class="mb-3">
    <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
    <input
        type="text"
        name="name"
        id="name"
        value="{{ old('name', $customer->name ?? '') }}"
        class="form-control @error('name') is-invalid @enderror"
        placeholder="e.g. John Doe"
        required
        autofocus
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="whatsapp_number" class="form-label">WhatsApp Number <span class="text-danger">*</span></label>
    <input
        type="text"
        name="whatsapp_number"
        id="whatsapp_number"
        value="{{ old('whatsapp_number', $customer->whatsapp_number ?? '') }}"
        class="form-control @error('whatsapp_number') is-invalid @enderror"
        placeholder="e.g. +91 98765 43210"
        required
    >
    <div class="form-text">International format allowed (for future WhatsApp integration).</div>
    @error('whatsapp_number')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-0">
    <label for="address" class="form-label">Address</label>
    <textarea
        name="address"
        id="address"
        rows="3"
        class="form-control @error('address') is-invalid @enderror"
        placeholder="Street, city, state (optional)"
    >{{ old('address', $customer->address ?? '') }}</textarea>
    @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
