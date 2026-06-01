{{-- Transporte --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Empresa de transporte <span class="text-danger">*</span></label>
    <input type="text" name="carrier"
           value="{{ old('carrier', $shipment->carrier ?? '') }}"
           class="form-control @error('carrier') is-invalid @enderror"
           placeholder="Ej: OCA, Andreani, Correo Argentino">
    @error('carrier')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Tracking --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Número de seguimiento</label>
    <input type="text" name="tracking_number"
           value="{{ old('tracking_number', $shipment->tracking_number ?? '') }}"
           class="form-control"
           placeholder="Ej: AR123456789">
</div>

{{-- Estado --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
    <select name="status" class="form-select">
        @foreach(\App\Models\Shipment::STATUSES as $value => $label)
            <option value="{{ $value }}"
                {{ old('status', $shipment->status ?? 'pendiente') == $value ? 'selected' : '' }}>
                {{ $label }}
            </option>
        @endforeach
    </select>
</div>

{{-- Fecha de envío --}}
<div class="mb-3">
    <label class="form-label fw-semibold">Fecha de envío</label>
    <input type="date" name="shipped_at"
           value="{{ old('shipped_at', isset($shipment) && $shipment->shipped_at ? $shipment->shipped_at->format('Y-m-d') : '') }}"
           class="form-control">
</div>