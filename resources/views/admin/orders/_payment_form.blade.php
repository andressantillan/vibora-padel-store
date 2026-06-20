@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Método --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Método de pago <span class="text-danger">*</span></label>
    <select name="method" class="form-select @error('method') is-invalid @enderror">
        <option value="">— Seleccioná un método —</option>
        @foreach(\App\Models\Payment::METHODS as $value => $label)
            <option value="{{ $value }}" {{ old('method') == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Fecha de pago --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Fecha de pago <span class="text-danger">*</span></label>
    <input type="date" name="paid_at"
           value="{{ old('paid_at', now()->format('Y-m-d')) }}"
           max="{{ now()->format('Y-m-d') }}"
           class="form-control @error('paid_at') is-invalid @enderror">
    @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>