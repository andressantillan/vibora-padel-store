<div class="row g-4">
    {{-- Columna izquierda --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold">Información general</div>
            <div class="card-body">

                {{-- Nombre --}}
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">
                        Nombre <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $product->name ?? '') }}"
                           class="form-control @error('name') is-invalid @enderror"
                           placeholder="Ej: Pala Head Delta Pro">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Descripción --}}
                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Descripción</label>
                    <textarea name="description"
                              id="description"
                              rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Descripción del producto...">{{ old('description', $product->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Shape y Level (solo paletas) --}}
                <div id="paleta-fields" style="display:none">
                    <hr>
                    <p class="text-muted fw-semibold mb-3">Especificaciones de paleta</p>

                    <div class="row g-2">
                        {{-- Forma --}}
                        <div class="col-md-6 mb-3">
                            <label for="shape" class="form-label fw-semibold">Forma</label>
                            <select name="shape" id="shape"
                                    class="form-select @error('shape') is-invalid @enderror">
                                <option value="">— Seleccioná una forma —</option>
                                @foreach(\App\Models\Product::SHAPES as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('shape', $product->shape ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shape')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Nivel --}}
                        <div class="col-md-6 mb-3">
                            <label for="level" class="form-label fw-semibold">Nivel</label>
                            <select name="level" id="level"
                                    class="form-select @error('level') is-invalid @enderror">
                                <option value="">— Seleccioná un nivel —</option>
                                @foreach(\App\Models\Product::LEVELS as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('level', $product->level ?? '') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Imágenes --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Imágenes</label>

                    {{-- Imágenes existentes en edición --}}
                    @if(!empty($product->images) && $product->images->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($product->images as $image)
                                <div class="position-relative text-center border border-secondary border-opacity-25 rounded p-2 bg-dark-subtle shadow-sm d-flex flex-column justify-content-between" style="width:120px; min-height:165px;">
                                    <div>
                                        <img src="{{ $image->url }}"
                                             class="existing-img-preview rounded mb-2 {{ $image->is_main ? 'border border-primary border-3' : 'shadow-sm' }}"
                                             style="width:100%;height:90px;object-fit:contain;cursor:pointer;">
                                        
                                        <div class="form-check d-flex justify-content-center align-items-center mb-2 p-0">
                                            <input type="radio" 
                                                   name="main_image" 
                                                   value="existing_{{ $image->id }}" 
                                                   class="form-check-input existing-main-radio m-0 cursor-pointer" 
                                                   id="main_existing_{{ $image->id }}"
                                                   {{ $image->is_main ? 'checked' : '' }}>
                                            <label for="main_existing_{{ $image->id }}" class="form-check-label ms-1 cursor-pointer fw-semibold text-light" style="font-size:0.75rem">Principal</label>
                                        </div>
                                    </div>
                                    <div class="form-check d-flex justify-content-center align-items-center m-0 p-0 mt-auto">
                                        <input type="checkbox"
                                               name="delete_images[]"
                                               value="{{ $image->id }}"
                                               class="form-check-input m-0"
                                               id="del_img_{{ $image->id }}">
                                        <label for="del_img_{{ $image->id }}"
                                               class="form-check-label ms-1 text-danger fw-semibold"
                                               style="font-size:0.75rem">Eliminar</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', () => {
                                const updateExistingHighlights = () => {
                                    document.querySelectorAll('.existing-img-preview').forEach(img => {
                                        img.classList.remove('border', 'border-primary', 'border-3');
                                        img.classList.add('shadow-sm');
                                    });
                                    // Highlight only the checked one
                                    document.querySelectorAll('.existing-main-radio:checked').forEach(radio => {
                                        const img = radio.closest('.position-relative').querySelector('.existing-img-preview');
                                        img.classList.remove('shadow-sm');
                                        img.classList.add('border', 'border-primary', 'border-3');
                                    });
                                };
                                
                                document.querySelectorAll('.existing-main-radio').forEach(radio => {
                                    radio.addEventListener('change', () => {
                                        // when an existing radio is checked, clear new radios highlights (handled by global name 'main_image')
                                        // trigger global highlight update
                                        updateExistingHighlights();
                                        if(typeof window.updateNewHighlights === 'function') window.updateNewHighlights();
                                    });
                                });
                                
                                document.querySelectorAll('.existing-img-preview').forEach(img => {
                                    img.addEventListener('click', () => {
                                        const radio = img.closest('.position-relative').querySelector('.existing-main-radio');
                                        radio.checked = true;
                                        radio.dispatchEvent(new Event('change'));
                                    });
                                });
                            });
                        </script>
                    @endif

                    <x-image-uploader name="images[]" :multiple="true" />
                </div>

            </div>
        </div>
    </div>

    {{-- Columna derecha --}}
    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header fw-semibold">Clasificación</div>
            <div class="card-body">

                {{-- Categoría --}}
                <div class="mb-3">
                    <label for="category_id" class="form-label fw-semibold">
                        Categoría <span class="text-danger">*</span>
                    </label>
                    <select name="category_id"
                            id="category_id"
                            class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">— Seleccioná una categoría —</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Marca --}}
                <div class="mb-3">
                    <label for="brand_id" class="form-label fw-semibold">
                        Marca <span class="text-danger">*</span>
                    </label>
                    <select name="brand_id"
                            id="brand_id"
                            class="form-select @error('brand_id') is-invalid @enderror">
                        <option value="">— Seleccioná una marca —</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ old('brand_id', $product->brand_id ?? '') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-header fw-semibold">Estado</div>
            <div class="card-body">
                <div class="form-check form-switch">
                    <input type="hidden" name="active" value="0">
                    <input type="checkbox"
                           name="active"
                           id="active"
                           value="1"
                           class="form-check-input"
                           {{ old('active', $product->active ?? true) ? 'checked' : '' }}>
                    <label for="active" class="form-check-label">Producto activo</label>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script para mostrar/ocultar campos de paleta --}}
<script>
    const categorySelect  = document.getElementById('category_id');
    const paletaFields    = document.getElementById('paleta-fields');
    const paletaCategoryId = "{{ \App\Models\Category::where('slug', 'paletas')->value('id') }}";

    function togglePaletaFields() {
        paletaFields.style.display = categorySelect.value == paletaCategoryId ? 'block' : 'none';
    }

    categorySelect.addEventListener('change', togglePaletaFields);
    togglePaletaFields(); // ejecutar al cargar en edición
</script>