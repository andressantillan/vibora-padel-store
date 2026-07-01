<div class="row g-3">
    {{-- Precio --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Precio <span class="text-danger">*</span></label>
        <div class="input-group has-validation">
            <span class="input-group-text">$</span>
            <input type="number" name="price"
                   value="{{ old('price', $variant->price ?? '') }}"
                   class="form-control @error('price') is-invalid @enderror"
                   step="0.01" min="0" placeholder="0.00">
            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Peso --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Peso (g)</label>
        <input type="number" name="weight"
               value="{{ old('weight', isset($variant->weight) ? (int)$variant->weight : '') }}"
               class="form-control @error('weight') is-invalid @enderror"
               step="1" min="1" placeholder="Ej: 370">
        @error('weight')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Color --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Color</label>
        <select name="color" class="form-select @error('color') is-invalid @enderror">
            <option value="">— Seleccionar color —</option>
            @foreach(['Negro', 'Blanco', 'Gris', 'Rojo', 'Azul', 'Verde', 'Amarillo', 'Naranja', 'Violeta', 'Rosa', 'Multicolor', 'Otro'] as $colorOption)
                <option value="{{ $colorOption }}" {{ old('color', $variant->color ?? '') == $colorOption ? 'selected' : '' }}>
                    {{ $colorOption }}
                </option>
            @endforeach
        </select>
        @error('color')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<hr class="my-4">

<div class="row g-3">
    {{-- Stock --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Stock actual <span class="text-danger">*</span></label>
        <input type="number" name="quantity"
               value="{{ old('quantity', $variant->stock->quantity ?? 0) }}"
               class="form-control @error('quantity') is-invalid @enderror" min="0">
        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    {{-- Stock mínimo --}}
    <div class="col-md-6">
        <label class="form-label fw-semibold">Stock mínimo <span class="text-danger">*</span></label>
        <input type="number" name="min_quantity"
               value="{{ old('min_quantity', $variant->stock->min_quantity ?? 5) }}"
               class="form-control @error('min_quantity') is-invalid @enderror" min="0">
        @error('min_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>