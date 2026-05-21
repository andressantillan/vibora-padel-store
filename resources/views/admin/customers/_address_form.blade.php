{{-- Calle --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Calle y número <span class="text-danger">*</span></label>
    <input type="text" name="street"
           value="{{ old('street') }}"
           class="form-control @error('street') is-invalid @enderror"
           placeholder="Ej: Av. Fontana 250">
    @error('street')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-2">
    {{-- Ciudad --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Ciudad <span class="text-danger">*</span></label>
        <input type="text" name="city"
               value="{{ old('city') }}"
               class="form-control @error('city') is-invalid @enderror"
               placeholder="Ej: Trelew">
        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Provincia --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Provincia <span class="text-danger">*</span></label>
        <input type="text" name="province"
               value="{{ old('province') }}"
               class="form-control @error('province') is-invalid @enderror"
               placeholder="Ej: Chubut">
        @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

{{-- Código postal --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Código postal <span class="text-danger">*</span></label>
    <input type="text" name="postal_code"
           value="{{ old('postal_code') }}"
           class="form-control @error('postal_code') is-invalid @enderror"
           placeholder="Ej: 9100">
    @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Predeterminada --}}
<div class="mb-3">
    <div class="form-check form-switch">
        <input type="hidden" name="is_default" value="0">
        <input type="checkbox" name="is_default" value="1"
               class="form-check-input" id="is_default_{{ $address->id ?? 'new' }}"
               {{ old('is_default') ? 'checked' : '' }}>
        <label for="is_default_{{ $address->id ?? 'new' }}" class="form-check-label">
            Marcar como predeterminada
        </label>
    </div>
</div>