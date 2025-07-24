<div class="container mt-3">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $block->name ?? '') }}">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <label class="form-label mt-3">Key</label>
                    <input type="text" name="key" class="form-control @error('key') is-invalid @enderror"
                        value="{{ old('key', $block->key ?? '') }}">
                    @error('key')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6"
                        placeholder="Enter a description...">{{ old('description', $block->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Content Items Section -->
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <button type="button" class="btn btn-outline-primary btn-sm" id="addMenuBtn">
                    <i class="fas fa-bars me-1"></i> Menu
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addLinkBtn">
                    <i class="fas fa-link me-1"></i> Link
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addTextBtn">
                    <i class="fas fa-align-left me-1"></i> Text
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addImageBtn">
                    <i class="fas fa-image me-1"></i> Image
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addHtmlBtn">
                    <i class="fas fa-code me-1"></i> HTML
                </button>
            </div>

            <div class="row">
                <div class="col-md-4" id="leftPanel">
                    <div id="contentItems"></div>
                    @error('content_items')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-8 bg-light border rounded p-3" id="rightPanel">
                    <p class="text-muted text-center mt-5" id="placeholderText">Select an item or create a new one.</p>
                </div>
            </div>
        </div>

        <div class="card-footer text-start bg-light">
            <input type="hidden" name="content_items" id="contentItemsInput"
                value="{{ isset($block) ? $block->attributes->toJson() : '' }}">
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>
</form>

<!-- Templates Section -->
<div id="templates" class="d-none">
    <!-- Menu Form Template -->
    <div id="menuForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="menu_name" placeholder="Enter menu name" required>
                <div class="invalid-feedback menu-name-error" style="display: none;">Menu name is required</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" class="form-control" name="menu_slug" placeholder="Enter slug" required>
                <div class="invalid-feedback menu-slug-error" style="display: none;">Menu slug is required</div>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <!-- Link Form Template -->
    <div id="linkForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="link_name" placeholder="Enter link name" required>
                <div class="invalid-feedback link-name-error" style="display: none;">Link name is required</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Slug / URL</label>
                <input type="text" class="form-control" name="link_slug" placeholder="Enter URL or slug" required>
                <div class="invalid-feedback link-slug-error" style="display: none;">URL is required</div>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <!-- Text Form Template -->
    <div id="textForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Text Content</label>
                <textarea class="form-control" name="text_content" rows="5" placeholder="Enter text..." required></textarea>
                <div class="invalid-feedback text-content-error" style="display: none;">Text content is required</div>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <!-- Image Form Template -->
    <div id="imageForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Upload Image</label>
                <input type="file" class="form-control" name="image_upload" accept="image/*">
                <small class="form-text text-muted mt-1" id="selectedFileName"></small>
                <div class="mt-2">
                    <img id="liveImagePreview" style="max-width: 100%; display: none;">
                </div>
                <input type="hidden" name="image_path" value="">
                <div class="invalid-feedback image-upload-error" style="display: none;">Image is required</div>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <!-- HTML Form Template -->
    <div id="htmlForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">HTML Content</label>
                <textarea class="form-control" name="html_content" rows="6" placeholder="Enter custom HTML..." required></textarea>
                <div class="invalid-feedback html-content-error" style="display: none;">HTML content is required</div>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>
</div>

<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        display: block;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
</style>

<script>
    $(document).ready(function() {
        let items = [];
        let currentItem = null;

        @if (isset($block))
            items = {!! json_encode(
                $block->attributes->map(function ($item) {
                        $type = $item->type;
                        $title = '';
                        $data = [];
            
                        switch ($type) {
                            case 'menu':
                                $title = $item->name ?? 'Untitled Menu';
                                $data = [
                                    'menu_name' => $item->name ?? '',
                                    'menu_slug' => $item->slug ?? '',
                                ];
                                break;
            
                            case 'link':
                                $title = $item->name ?? 'Untitled Link';
                                $data = [
                                    'link_name' => $item->name ?? '',
                                    'link_slug' => $item->slug ?? '',
                                ];
                                break;
            
                            case 'text':
                                $text = $item->text ?? '';
                                $title = strlen($text) > 20 ? substr($text, 0, 20) + '...' : $text;
                                $data = [
                                    'text_content' => $text,
                                ];
                                break;
            
                            case 'image':
                                $title = $item->name ?? 'Image';
                                $imageUrl = $item->image_path ? asset('storage/' . $item->image_path) : '';
                                $data = [
                                    'image_url' => $imageUrl,
                                    'image_alt' => $item->name ?? 'Image',
                                    'image_path' => $item->image_path ?? '',
                                    'image_upload' => null,
                                ];
                                break;
            
                            case 'html':
                                $title = 'HTML Content';
                                $data = [
                                    'html_content' => $item->html ?? '',
                                ];
                                break;
                        }
            
                        return [
                            'id' => $item->id,
                            'type' => $type,
                            'title' => $title,
                            'data' => $data,
                        ];
                    })->toArray(),
            ) !!};

            renderItems();

            if (items.length > 0) {
                setTimeout(() => {
                    $('.content-item').first().click();
                }, 100);
            }
        @endif

        function renderItems() {
            const $list = $('#contentItems');
            $list.html('');
            items.forEach(item => {
                const $div = $('<div>')
                    .addClass('content-item border p-2 mb-2 cursor-pointer')
                    .attr('data-id', item.id);

                if (item.type === 'image') {
                    const imgSrc = item.data.image_url || 'https://via.placeholder.com/50';
                    $div.append(
                        $('<div>').addClass('d-flex align-items-center').append(
                            $('<img>').attr('src', imgSrc).css({
                                'width': '30px',
                                'height': '30px',
                                'object-fit': 'cover',
                                'margin-right': '10px'
                            }),
                            $('<span>').text(item.title)
                        )
                    );
                } else {
                    $div.text(item.title);
                }

                $div.on('click', function() {
                    $('.content-item').removeClass('active');
                    $(this).addClass('active');
                    currentItem = items.find(i => i.id === item.id);
                    loadForm(currentItem.type + 'Form', currentItem);
                });
                $list.append($div);
            });
        }

        function loadForm(templateId, item = null) {
            const $panel = $('#rightPanel');
            $('#placeholderText').remove();
            const $template = $('#' + templateId);

            if ($template.length) {
                $panel.html('');
                const $clone = $template.clone().removeClass('d-none').attr('id', '');

                // Clear any previous file inputs
                $clone.find('input[type="file"]').val('');

                if (item) {
                    Object.keys(item.data).forEach(key => {
                        const value = item.data[key];
                        if (value !== null && value !== undefined) {
                            if (key === 'image_url' && item.type === 'image') {
                                // For image items, show the preview
                                $clone.find('#liveImagePreview').attr('src', value).show();
                                $clone.find('#selectedFileName').text(value.split('/').pop());
                            } else {
                                $clone.find(`[name="${key}"]`).val(value);
                            }
                        }
                    });
                }

                $panel.append($clone);

                $clone.find('.save-btn').on('click', function() {
                    saveItem(templateId, item ? item.id : null);
                });

                // Handle file input changes only for image form
                if (templateId === 'imageForm') {
                    $clone.find('input[type="file"]').on('change', function(event) {
                        const file = event.target.files[0];
                        const reader = new FileReader();
                        if (file) {
                            reader.onload = function(e) {
                                $clone.find('#liveImagePreview').attr('src', e.target.result)
                                    .show();
                                $clone.find('#selectedFileName').text(file.name);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                }
            }
        }

        function saveItem(type, itemId = null) {
            const $form = $('#rightPanel form');
            let isValid = true;

            // Clear previous errors
            $form.find('.is-invalid').removeClass('is-invalid');
            $form.find('.invalid-feedback').hide();

            const formData = {};
            $form.serializeArray().forEach(item => {
                formData[item.name] = item.value;
            });

            $form.find('input[type="file"]').each(function() {
                if (this.files.length > 0) {
                    formData[this.name] = this.files[0];
                }
            });

            let title = '';
            const typePrefix = type.replace('Form', '').toLowerCase();

            // Validate based on type
            switch (typePrefix) {
                case 'menu':
                    if (!formData['menu_name']?.trim()) {
                        isValid = false;
                        $form.find('[name="menu_name"]').addClass('is-invalid');
                        $form.find('.menu-name-error').show();
                    }
                    if (!formData['menu_slug']?.trim()) {
                        isValid = false;
                        $form.find('[name="menu_slug"]').addClass('is-invalid');
                        $form.find('.menu-slug-error').show();
                    }
                    title = formData['menu_name'] || 'Untitled Menu';
                    break;

                case 'link':
                    if (!formData['link_name']?.trim()) {
                        isValid = false;
                        $form.find('[name="link_name"]').addClass('is-invalid');
                        $form.find('.link-name-error').show();
                    }
                    if (!formData['link_slug']?.trim()) {
                        isValid = false;
                        $form.find('[name="link_slug"]').addClass('is-invalid');
                        $form.find('.link-slug-error').show();
                    }
                    title = formData['link_name'] || 'Untitled Link';
                    break;

                case 'text':
                    if (!formData['text_content']?.trim()) {
                        isValid = false;
                        $form.find('[name="text_content"]').addClass('is-invalid');
                        $form.find('.text-content-error').show();
                    }
                    title = formData.text_content ?
                        (formData.text_content.length > 20 ? formData.text_content.substring(0, 20) + '...' :
                            formData.text_content) :
                        'Untitled Text';
                    break;

                case 'image':
                    const fileInput = $form.find('input[type="file"]')[0];
                    if (!fileInput?.files?.length && !formData.image_path) {
                        isValid = false;
                        $form.find('input[type="file"]').addClass('is-invalid');
                        $form.find('.image-upload-error').show();
                    }
                    title = formData.image_alt || 'Image';
                    break;

                case 'html':
                    if (!formData['html_content']?.trim()) {
                        isValid = false;
                        $form.find('[name="html_content"]').addClass('is-invalid');
                        $form.find('.html-content-error').show();
                    }
                    title = 'HTML Content';
                    break;
            }

            if (!isValid) {
                return false;
            }

            // Process image if needed
            if (typePrefix === 'image') {
                const fileInput = $form.find('input[type="file"]')[0];
                const files = fileInput?.files;

                if (files && files.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        formData.image_url = e.target.result;
                        formData.image_alt = formData.image_alt || 'Image';
                        finalizeSave();
                    };
                    reader.readAsDataURL(files[0]);
                    return; // Wait for reader to complete
                } else if (itemId) {
                    const existingItem = items.find(item => item.id === itemId);
                    if (existingItem && existingItem.data.image_url) {
                        formData.image_url = existingItem.data.image_url;
                    }
                }
            }

            function finalizeSave() {
                if (itemId) {
                    const index = items.findIndex(item => item.id === itemId);
                    if (index !== -1) {
                        items[index].data = formData;
                        items[index].title = title;
                    }
                } else {
                    const newItem = {
                        id: Date.now().toString(),
                        type: typePrefix,
                        title: title,
                        data: formData
                    };
                    items.push(newItem);
                }

                renderItems();
                $('#rightPanel').html(
                    '<p class="text-muted text-center mt-5" id="placeholderText">Select an item or create a new one.</p>'
                );
            }

            finalizeSave();
        }


        $('form').on('submit', function(e) {
            let isValid = true;

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('#contentItems .alert-danger').remove();

            const $nameInput = $('input[name="name"]');
            if (!$nameInput.val().trim()) {
                isValid = false;
                $nameInput.addClass('is-invalid')
                    .after('<div class="invalid-feedback">Name is required</div>');
            }

            const $keyInput = $('input[name="key"]');
            if (!$keyInput.val().trim()) {
                isValid = false;
                $keyInput.addClass('is-invalid')
                    .after('<div class="invalid-feedback">Key is required</div>');
            } else if (!$keyInput.val().match(/^[a-z0-9._-]+$/i)) {
                isValid = false;
                $keyInput.addClass('is-invalid')
                    .after(
                        '<div class="invalid-feedback">Key can only contain letters, numbers, dots, hyphens and underscores</div>'
                    );
            }


            const $descriptionInput = $('textarea[name="description"]');
            if (!$descriptionInput.val().trim()) {
                isValid = false;
                $descriptionInput.addClass('is-invalid')
                    .after('<div class="invalid-feedback">Description is required</div>');
            }


            if (items.length === 0) {
                isValid = false;
                $('#contentItems').prepend(
                    '<div class="alert alert-danger">Please add at least one content item</div>');
            }

            if (!isValid) {
                e.preventDefault();
                return false;
            }

            $('#contentItemsInput').val(JSON.stringify(items));
        });

        $(document).on('input', 'input[name="name"]', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
        });

        $(document).on('input', 'input[name="key"]', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
        });

        $(document).on('input', 'textarea[name="description"]', function() {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').hide();
        });

        $('#addMenuBtn').on('click', () => {
            currentItem = null;
            loadForm('menuForm');
        });
        $('#addLinkBtn').on('click', () => {
            currentItem = null;
            loadForm('linkForm');
        });
        $('#addTextBtn').on('click', () => {
            currentItem = null;
            loadForm('textForm');
        });
        $('#addImageBtn').on('click', () => {
            currentItem = null;
            loadForm('imageForm');
        });
        $('#addHtmlBtn').on('click', () => {
            currentItem = null;
            loadForm('htmlForm');
        });
    });
</script>
