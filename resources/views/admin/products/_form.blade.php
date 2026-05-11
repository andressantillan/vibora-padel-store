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

                {{-- Imágenes --}}
                <div class="mb-3">
                    <label class="form-label fw-semibold">Imágenes</label>

                    {{-- Imágenes existentes en edición --}}
                    @if(!empty($product->images) && $product->images->isNotEmpty())
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($product->images as $image)
                                <div class="position-relative text-center" style="width:90px">
                                    <img src="{{ $image->url }}"
                                         class="rounded border mb-1 {{ $image->is_main ? 'border-primary border-2' : '' }}"
                                         style="width:80px;height:80px;object-fit:contain">
                                    @if($image->is_main)
                                        <span class="badge bg-primary d-block mb-1" style="font-size:0.65rem">Principal</span>
                                    @endif
                                    <div class="form-check d-flex justify-content-center">
                                        <input type="checkbox"
                                               name="delete_images[]"
                                               value="{{ $image->id }}"
                                               class="form-check-input"
                                               id="del_img_{{ $image->id }}">
                                        <label for="del_img_{{ $image->id }}"
                                               class="form-check-label ms-1 text-danger"
                                               style="font-size:0.75rem">Eliminar</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <input type="file"
                           name="images[]"
                           id="images"
                           accept="image/*"
                           multiple
                           class="form-control @error('images') is-invalid @enderror">
                    <div class="form-text">Podés subir múltiples imágenes. La primera será la principal.</div>
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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