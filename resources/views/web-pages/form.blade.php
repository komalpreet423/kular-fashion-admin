<div class="card">
    <div class="card-body">
        <h4 class="card-title">Web Page</h4>
        <div class="row">
            <div class="col-md-4 mb-2">
                <x-form-input name="title" label="Page Title" value="{{ old('title', $webPage->title ?? '') }}"
                    placeholder="Enter Page Title" required="true" />
            </div>
            <div class="col-md-4">
                <x-form-input name="slug" label="Slug" value="{{ old('slug', $webPage->slug ?? '') }}"
                    placeholder="Enter Slug" />
            </div>
            @if (isset($webPage) && $webPage->exists)
                <div class="col-md-4">
                    <x-form-input type="datetime-local" class="date-picker-publish" name="published_at"
                        label="Published Timestamp" :value="old(
                            'published_at',
                            $webPage->published_at
                                ? \Carbon\Carbon::parse($webPage->published_at)->format('Y-m-d\TH:i')
                                : '',
                        )" disabled="true" />
                </div>
            @endif
        </div>

        <h4 class="card-title mt-2">Page Content</h4>
        <textarea name="page_content" class="form-control editor" rows="5" placeholder="Enter Page Content">{{ old('page_content', $webPage->page_content ?? '') }}</textarea>
    </div>
</div>

<!-- Listing Page Rules -->
<div class="card">
    <div class="card-body p-4">
        <h4 class="card-title">Listing Page Rules</h4>
        @php
            $rulesInput = old('rules', $webPage->rules ?? []);
            $rules = is_array($rulesInput)
                ? $rulesInput
                : (is_string($rulesInput)
                    ? json_decode($rulesInput, true) ?? []
                    : []);
        @endphp
        <div class="rule-group mb-2">
            @foreach ([
        'rule' => 'Rule',
        'must' => 'Must',
        'have_tags' => 'Have one of these tags',
        'collections' => 'Collections transitional wear',
    ] as $key => $label)
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="rules[]" id="{{ $key }}"
                        value="{{ $key }}" {{ in_array($key, $rules) ? 'checked' : '' }}>
                    <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Listing Page Content -->
<div class="card">
    <div class="card-body p-3">
        <h4 class="card-title">Listing Page Content</h4>

        <h4 class="card-title mt-3">Description</h4>
        <textarea name="description" id="description" class="form-control editor" rows="5"
            placeholder="Enter Description">{{ old('description', $webPage->description ?? '') }}</textarea>
        <div class="text-muted mt-1">words: <span id="wordCount">0</span> | chars: <span id="charCount">0</span></div>

        <h4 class="card-title mt-2">Summary</h4>
        <textarea name="summary" id="summary" class="form-control editor" rows="5" placeholder="Enter Summary">{{ old('summary', $webPage->summary ?? '') }}</textarea>
        <div class="row mt-4">
            @foreach (['small', 'medium', 'large'] as $size)
                <div class="col-md-4 mb-3">
                    <div class="upload-area">
                        <label class="form-label">{{ ucfirst($size) }} Image</label>
                        <div class="dropzone">
                            <input type="file" name="image_{{ $size }}" id="image_{{ $size }}"
                                class="file-input" accept="image/*"
                                onchange="previewImage(event, '{{ $size }}')">

                            <div class="dropzone-content" id="preview_{{ $size }}">
                                @if (!empty($webPage) && !empty($webPage->{'image_' . $size}))
                                    <img src="{{ asset('assets/images/' . $webPage->{'image_' . $size}) }}"
                                        alt="{{ $size }} image" class="img-thumbnail uploaded-image"
                                        width="150">
                                    <p>{{ basename($webPage->{'image_' . $size}) }}</p>
                                @else
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Drop file here to upload</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Listing Page Options -->
<div class="card">
    <div class="card-body p-4">
        <h4 class="card-title">Listing Page Options</h4>
        @foreach ([
        'hide_categories' => 'Hide categories',
        'hide_all_filters' => 'Hide all filters',
        'show_all_filters' => 'Show all filters',
    ] as $key => $label)
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="{{ $key }}" id="{{ $key }}"
                    {{ old($key, $webPage->$key ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $key }}">{{ $label }}</label>
            </div>
        @endforeach
        @php
            $filterMode = old('filter_mode') ?? ($webPage->filter_mode ?? '');
            $filtersInput = old('filters', $webPage->filters ?? []);
            $selectedFilters = is_array($filtersInput)
                ? $filtersInput
                : (is_string($filtersInput)
                    ? json_decode($filtersInput, true) ?? []
                    : []);
        @endphp

        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="filter_mode" id="filter_mode_show_some"
                value="show_some" {{ $filterMode === 'show_some' || count($selectedFilters) > 0 ? 'checked' : '' }}>
            <label class="form-check-label" for="filter_mode_show_some">Only show some filters</label>
        </div>

        <div id="filter-list" class="mt-1" style="{{ $filterMode === 'show_some' ? '' : 'display: none;' }}">
            <div class="row">
                @foreach (['Manufacturer', 'Product Type', 'Gender', 'Colour', 'Shoe Style', 'Size'] as $filter)
                    @php
                        $key = strtolower(str_replace(' ', '_', $filter));
                    @endphp
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="filters[]"
                                value="{{ $key }}" id="filters_{{ $key }}"
                                {{ in_array($key, $selectedFilters) ? 'checked' : '' }}>
                            <label class="form-check-label"
                                for="filters_{{ $key }}">{{ $filter }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- SEO Section -->
<div class="card">
    <div class="card-body p-4">
        <h4 class="card-title">Search Engine Optimization (SEO)</h4>
        <div class="row">
            <div class="col-sm-10 mb-2">
                <x-form-input name="heading" label="Heading" value="{{ old('heading', $webPage->heading ?? '') }}"
                    placeholder="Heading" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="mb-2">
                    <x-form-input name="meta_title" label="Meta Title"
                        value="{{ old('meta_title', $webPage->meta_title ?? '') }}" placeholder="Meta Title" />
                </div>
                <div class="mb-2">
                    <x-form-input name="meta_keywords" label="Meta Keywords"
                        value="{{ old('meta_keywords', $webPage->meta_keywords ?? '') }}"
                        placeholder="Meta Keywords" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-2">
                    <label for="meta_description">Meta Description</label>
                    <textarea name="meta_description" class="form-control" id="meta_description" rows="5"
                        placeholder="Meta Description">{{ old('meta_description', $webPage->meta_description ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<button type="submit" class="btn btn-primary w-md">Submit</button>
<x-include-plugins :plugins="['chosen', 'datePicker', 'contentEditor']" />
<style>
    .upload-area {
        text-align: center;
    }

    .dropzone {
        border: 2px dashed #ccc;
        border-radius: 5px;
        padding: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .dropzone:hover {
        border-color: #999;
        background-color: #f8f9fa;
    }

    .dropzone .file-input {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .uploaded-image {
        width: 200px !important;
        height: auto;
        max-height: 300px;
        object-fit: contain;
        display: block;
        margin: 0 auto 0px;
    }

    .form-check {
        padding-left: 1.5em;
    }
</style>
<script>
    $(document).ready(function() {
        $('.dropzone').each(function() {
            const dropzone = $(this);
            const fileInput = dropzone.find('.file-input');
            const previewBox = dropzone.find('.dropzone-content');

            // Click opens file picker
            dropzone.on('click', function(e) {
                if (!$(e.target).is('input[type="file"]')) {
                    fileInput.trigger('click');
                }
            });

            // File input change shows preview
            fileInput.on('change', function() {
                if (this.files.length > 0) {
                    showPreview(this.files[0], previewBox);
                }
            });

            function showPreview(file, target) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    target.html(
                        `<img src="${e.target.result}" class="img-thumbnail uploaded-image" width="150">`
                    );
                };
                reader.readAsDataURL(file);
            }
        });

        // Filter toggle logic
        function toggleFilterList() {
            if ($('#filter_mode_show_some').is(':checked')) {
                $('#filter-list').show();
            } else {
                $('#filter-list').hide();
            }
        }

        // Ensure state is applied on page load
        $(document).ready(function() {
            toggleFilterList();

            // Attach change event
            $('#filter_mode_show_some').on('change', toggleFilterList);
        });
    });
</script>
