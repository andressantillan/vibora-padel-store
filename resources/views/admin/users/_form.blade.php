<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Datos de acceso</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $user->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email"
                           value="{{ old('email', $user->email ?? '') }}"
                           class="form-control @error('email') is-invalid @enderror">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <x-password-input
                    name="password" id="password" label="Contraseña"
                    :required="!isset($user)"
                    :hint="isset($user) ? 'Dejá en blanco para mantener la actual.' : null" />

                <x-password-input
                    name="password_confirmation" id="password_confirmation" label="Confirmar contraseña" />
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-semibold">Rol</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="role" class="form-label fw-semibold">Rol asignado <span class="text-danger">*</span></label>
                    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror">
                        <option value="">— Seleccioná un rol —</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ old('role', isset($user) ? $user->roles->first()?->name : '') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="alert alert-light small mb-0">
                    <strong>Admin:</strong> acceso total.<br>
                    <strong>Vendedor:</strong> pedidos, pagos y clientes.<br>
                    <strong>Depósito:</strong> catálogo, stock y envíos.
                </div>
            </div>
        </div>
    </div>
</div>