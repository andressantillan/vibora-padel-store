<div class="row g-4">
    {{-- Datos de acceso --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Datos de acceso</div>
            <div class="card-body">

                {{-- Nombre --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        Nombre <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $customer->user->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        Email <span class="text-danger">*</span>
                    </label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $customer->user->email ?? '') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Password --}}
                <x-password-input
                    name="password"
                    id="password"
                    label="Contraseña"
                    :required="!isset($customer)"
                    :hint="isset($customer) ? 'Dejá en blanco para mantener la contraseña actual.' : null" />

                {{-- Confirmar password --}}
                <x-password-input
                    name="password_confirmation"
                    id="password_confirmation"
                    label="Confirmar contraseña" />
            </div>
        </div>
    </div>

    {{-- Datos personales --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Datos personales</div>
            <div class="card-body">

                {{-- DNI --}}
                <div class="mb-3">
                    <label for="dni" class="form-label fw-semibold">DNI</label>
                    <input type="text" name="dni" id="dni"
                           value="{{ old('dni', $customer->dni ?? '') }}"
                           class="form-control @error('dni') is-invalid @enderror"
                           placeholder="Ej: 35123456">
                    @error('dni')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Teléfono --}}
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Teléfono</label>
                    <input type="text" name="phone" id="phone"
                           value="{{ old('phone', $customer->phone ?? '') }}"
                           class="form-control @error('phone') is-invalid @enderror"
                           placeholder="Ej: 2804123456">
                    <div class="form-text">Debe contener solo números, sin espacios ni guiones.</div>
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Fecha de nacimiento --}}
                <div class="mb-3">
                    <label for="birth_date" class="form-label fw-semibold">Fecha de nacimiento</label>
                    <input type="date" name="birth_date" id="birth_date"
                           value="{{ old('birth_date', isset($customer->birth_date) ? $customer->birth_date->format('Y-m-d') : '') }}"
                           class="form-control @error('birth_date') is-invalid @enderror">
                    @error('birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

            </div>
        </div>
    </div>
</div>