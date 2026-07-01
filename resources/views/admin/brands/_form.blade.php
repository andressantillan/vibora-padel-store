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
    @if(!empty($brand->logo_url))
        <div class="mb-2">
            <img src="{{ $brand->logo_url }}"
                 alt="Logo actual"
                 class="rounded border"
                 style="height:60px;object-fit:contain">
            <small class="text-muted d-block mt-1">Logo actual. Subí uno nuevo para reemplazarlo.</small>
        </div>
    @endif
    <x-image-uploader name="logo" :multiple="false" />
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