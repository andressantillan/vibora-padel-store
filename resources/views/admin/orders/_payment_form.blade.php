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
    <label class="form-label fw-semibold">Método <span class="text-danger">*</span></label>
    <select name="method" class="form-select @error('method') is-invalid @enderror">
        @foreach(\App\Models\Payment::METHODS as $value => $label)
            <option value="{{ $value }}" {{ old('method') == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
    @error('method')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Monto --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
        <span class="input-group-text">$</span>
        <input type="number" name="amount"
               value="{{ old('amount', $orderTotal ?? '') }}"
               class="form-control @error('amount') is-invalid @enderror"
               step="0.01" min="0">
        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

{{-- Estado --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
    <select name="status" class="form-select">
        @foreach(\App\Models\Payment::STATUSES as $value => $label)
            <option value="{{ $value }}" {{ old('status', 'pendiente') == $value ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
    </select>
</div>

{{-- Referencia --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Referencia</label>
    <input type="text" name="reference" value="{{ old('reference') }}"
           class="form-control" placeholder="Ej: N° de operación">
</div>

{{-- Fecha de pago --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Fecha de pago</label>
    <input type="date" name="paid_at"
           value="{{ old('paid_at', isset($payment) && $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '') }}"
           class="form-control">
</div>