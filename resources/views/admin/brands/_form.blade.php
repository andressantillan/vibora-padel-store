{{-- Nombre --}}
<div class="mb-3">
    <label for="name" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
    <input type="text"
           name="name"
           id="name"
           value="{{ old('name', $brand->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           placeholder="Ej: Bullpadel">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Logo --}}
<div class="mb-3">
    <label for="logo" class="form-label fw-semibold">Logo</label>
    @if(!empty($brand->logo))
        <div class="mb-2">
            <img src="{{ Storage::url($brand->logo) }}"
                 alt="Logo actual"
                 class="rounded border"
                 style="height:60px;object-fit:contain">
            <small class="text-muted d-block mt-1">Logo actual. Subí uno nuevo para reemplazarlo.</small>
        </div>
    @endif
    <input type="file"
           name="logo"
           id="logo"
           accept="image/*"
           class="form-control @error('logo') is-invalid @enderror">
    @error('logo')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Estado --}}
<div class="mb-3">
    <div class="form-check form-switch">
        <input type="hidden" name="active" value="0">
        <input type="checkbox"
               name="active"
               id="active"
               value="1"
               class="form-check-input"
               {{ old('active', $brand->active ?? true) ? 'checked' : '' }}>
        <label for="active" class="form-check-label">Marca activa</label>
    </div>
</div>