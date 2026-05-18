{{-- Nombre --}}
<div class="mb-3">
    <label for="name" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
    <input type="text"
           name="name"
           id="name"
           value="{{ old('name', $category->name ?? '') }}"
           class="form-control @error('name') is-invalid @enderror"
           placeholder="Ej: Palas de control">
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Descripción --}}
<div class="mb-3">
    <label for="description" class="form-label fw-semibold">Descripción</label>
    <textarea name="description"
              id="description"
              rows="3"
              class="form-control @error('description') is-invalid @enderror"
              placeholder="Descripción opcional de la categoría">{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
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
               {{ old('active', $category->active ?? true) ? 'checked' : '' }}>
        <label for="active" class="form-check-label">Categoría activa</label>
    </div>
</div>