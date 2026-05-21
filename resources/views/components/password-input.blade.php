<div class="mb-3">
    <label for="{{ $id }}" class="form-label fw-semibold">
        {{ $label }}
        @if($required)<span class="text-danger">*</span>@endif
    </label>

    <div class="input-group @error($name) has-validation @enderror">
        <input type="password"
               name="{{ $name }}"
               id="{{ $id }}"
               class="form-control @error($name) is-invalid @enderror"
               autocomplete="new-password">
        <button type="button"
                class="btn btn-outline-secondary toggle-password"
                data-target="{{ $id }}"
                tabindex="-1">
            <i class="bi bi-eye"></i>
        </button>
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    @if($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>