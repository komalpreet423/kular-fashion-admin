<div class="container mt-">
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $block->name ?? '' }}">

                    <label class="form-label mt-3">Key</label>
                    <input type="text" name="key" class="form-control" placeholder="footer.links"
                        value="{{ $block->key ?? '' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="6" placeholder="Enter a description...">{{ $block->description ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>

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
<div id="templates" class="d-none">
    <div id="menuForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="menu_name" placeholder="Enter menu name">
            </div>
            <div class="mb-3">
                <label class="form-label">Slug</label>
                <input type="text" class="form-control" name="menu_slug" placeholder="Enter slug">
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <div id="linkForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" name="link_name" placeholder="Enter link name">
            </div>
            <div class="mb-3">
                <label class="form-label">Slug / URL</label>
                <input type="text" class="form-control" name="link_slug" placeholder="Enter URL or slug">
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <div id="textForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">Text Content</label>
                <textarea class="form-control" name="text_content" rows="5" placeholder="Enter text..."></textarea>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

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
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>

    <div id="htmlForm">
        <form class="w-100">
            <div class="mb-3">
                <label class="form-label">HTML Content</label>
                <textarea class="form-control" name="html_content" rows="6" placeholder="Enter custom HTML..."></textarea>
            </div>
            <button type="button" class="btn btn-sm btn-success save-btn">Save Item</button>
        </form>
    </div>
</div>

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
                                $title = strlen($text) > 20 ? substr($text, 0, 20) . '...' : $text;
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
            const formData = {};
            const $form = $('#rightPanel form');

            // Handle regular form fields
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

            switch (typePrefix) {
                case 'menu':
                    title = formData['menu_name'] || 'Untitled Menu';
                    break;
                case 'link':
                    title = formData['link_name'] || 'Untitled Link';
                    break;
                case 'text':
                    title = formData.text_content ?
                        (formData.text_content.length > 20 ? formData.text_content.substring(0, 20) + '...' :
                            formData.text_content) :
                        'Untitled Text';
                    break;
                case 'image':
                    const fileInput = $form.find('input[type="file"]')[0];
                    const files = fileInput?.files;

                    if (files && files.length > 0) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            formData.image_url = e.target.result;
                            formData.image_alt = formData.image_alt || 'Image';
                            finalizeImageSave();
                        };
                        reader.readAsDataURL(files[0]);
                        return; // Wait for reader to complete
                    } else if (itemId) {
                        // Keep existing image if no new file is selected
                        const existingItem = items.find(item => item.id === itemId);
                        if (existingItem && existingItem.data.image_url) {
                            formData.image_url = existingItem.data.image_url;
                        }
                    }

                    title = formData.image_alt || 'Image';
                    break;
                case 'html':
                    title = 'HTML Content';
                    break;
            }

            function finalizeImageSave() {
                title = formData.image_alt || 'Image';
                finalizeSave();
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


            if (typePrefix !== 'image' || !fileInput?.files?.length) {
                finalizeSave();
            }
        }

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

        $('form').on('submit', function() {
            $('#contentItemsInput').val(JSON.stringify(items));
        });
    });
</script>
