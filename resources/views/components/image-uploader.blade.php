@props(['name', 'multiple' => false, 'id' => 'image_uploader_'.uniqid()])

<div class="image-uploader-wrapper">
    <input type="file"
           name="{{ $name }}"
           id="{{ $id }}"
           accept="image/*"
           {{ $multiple ? 'multiple' : '' }}
           class="form-control @error(str_replace('[]', '', $name)) is-invalid @enderror"
           onchange="previewUploaderImages_{{ $id }}(this)">
           
    @error(str_replace('[]', '', $name))
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if($multiple)
        <div class="form-text mt-1 text-muted">Podés elegir la imagen principal seleccionándola abajo.</div>
    @endif

    <div id="preview_{{ $id }}" class="d-flex flex-wrap gap-2 mt-3 empty-hide"></div>
</div>

<script>
    function previewUploaderImages_{{ $id }}(input) {
        const previewContainer = document.getElementById('preview_{{ $id }}');
        previewContainer.innerHTML = '';
        
        if (!input.files || input.files.length === 0) {
            return;
        }

        Array.from(input.files).forEach((file, index) => {
            const url = URL.createObjectURL(file);
            const isMultiple = {{ $multiple ? 'true' : 'false' }};
            
            const card = document.createElement('div');
            card.className = 'position-relative text-center border border-secondary border-opacity-25 rounded p-2 bg-dark-subtle shadow-sm transition-all';
            card.style.width = '120px';
            
            const img = document.createElement('img');
            img.src = url;
            img.className = 'rounded mb-2 shadow-sm transition-all';
            img.style.width = '100%';
            img.style.height = '100px';
            img.style.objectFit = 'contain';
            
            card.appendChild(img);
            
            if (isMultiple) {
                const radioWrapper = document.createElement('div');
                radioWrapper.className = 'form-check d-flex justify-content-center align-items-center m-0 p-0';
                
                const radioInput = document.createElement('input');
                radioInput.type = 'radio';
                radioInput.name = 'main_image';
                radioInput.value = 'new_' + index;
                radioInput.className = 'form-check-input m-0 cursor-pointer new-main-radio';
                radioInput.id = 'main_img_{{ $id }}_' + index;
                
                // If there are no existing images, we can check the first new one
                const hasExisting = document.querySelector('.existing-main-radio') !== null;
                if (!hasExisting && index === 0) {
                    radioInput.checked = true;
                }
                
                const radioLabel = document.createElement('label');
                radioLabel.htmlFor = 'main_img_{{ $id }}_' + index;
                radioLabel.className = 'form-check-label ms-1 cursor-pointer fw-semibold text-light';
                radioLabel.style.fontSize = '0.75rem';
                radioLabel.innerText = 'Principal';
                
                radioWrapper.appendChild(radioInput);
                radioWrapper.appendChild(radioLabel);
                card.appendChild(radioWrapper);
                
                // Clicking the image also checks the radio
                img.addEventListener('click', () => {
                    radioInput.checked = true;
                    radioInput.dispatchEvent(new Event('change'));
                });
                img.style.cursor = 'pointer';
            }
            
            previewContainer.appendChild(card);
        });

        // Global highlight function for new images
        window.updateNewHighlights = () => {
            document.querySelectorAll('#preview_{{ $id }} img').forEach(el => {
                el.classList.remove('border', 'border-primary', 'border-3');
            });
            document.querySelectorAll('#preview_{{ $id }} .new-main-radio:checked').forEach(radio => {
                const img = radio.closest('.position-relative').querySelector('img');
                img.classList.add('border', 'border-primary', 'border-3');
            });
        };

        // Attach change listener to newly created radios
        document.querySelectorAll('.new-main-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                window.updateNewHighlights();
                // Clear existing highlights if an existing image radio is unchecked by this action
                if(typeof updateExistingHighlights === 'function') updateExistingHighlights();
            });
        });

        // Initial highlight run
        window.updateNewHighlights();
    }
</script>
