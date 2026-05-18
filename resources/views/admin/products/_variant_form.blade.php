{{-- Precio --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
    <div class="input-group">
        <span class="input-group-text">$</span>
        <input type="number"
               name="price"
               value="{{ old('price', $variant->price ?? '') }}"
               class="form-control @error('price') is-invalid @enderror"
               step="0.01" min="0"
               placeholder="0.00">
    </div>
    @error('price')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="row g-2">
    {{-- Color --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Color</label>
        <input type="text"
               name="color"
               value="{{ old('color', $variant->color ?? '') }}"
               class="form-control"
               placeholder="Ej: Negro">
    </div>

    {{-- Talle --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Talle</label>
        <input type="text"
               name="size"
               value="{{ old('size', $variant->size ?? '') }}"
               class="form-control"
               placeholder="Ej: S, M, L, XL">
    </div>
</div>

{{-- Peso --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Peso (kg)</label>
    <input type="number"
           name="weight"
           value="{{ old('weight', $variant->weight ?? '') }}"
           class="form-control"
           step="0.01" min="0"
           placeholder="Ej: 0.37">
</div>

<hr>

<div class="row g-2">
    {{-- Stock --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Stock actual <span class="text-danger">*</span></label>
        <input type="number"
               name="quantity"
               value="{{ old('quantity', $variant->stock->quantity ?? 0) }}"
               class="form-control @error('quantity') is-invalid @enderror"
               min="0">
        @error('quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Stock mínimo --}}
    <div class="col-md-6 mb-3">
        <label class="form-label fw-semibold">Stock mínimo <span class="text-danger">*</span></label>
        <input type="number"
               name="min_quantity"
               value="{{ old('min_quantity', $variant->stock->min_quantity ?? 5) }}"
               class="form-control @error('min_quantity') is-invalid @enderror"
               min="0">
        @error('min_quantity')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>