<div class="card">
    <div class="card-body">
        <h4 class="card-title">Basic Details</h4>

        <div class="row mb-2">
            <div class="col-sm-6 col-md-3">
                <div class="mb-3">
                    <x-form-input name="name" value="{{ $brand->name ?? '' }}" label="Brand Name"
                        placeholder="Enter Brand Name" required="true" />
                </div>
            </div>

            <div class="col-sm-6 col-md-3">
                <div class="mb-3">
                    <x-form-input name="short_name" value="{{ $brand->short_name ?? '' }}" label="Short Name"
                        placeholder="Enter Short Name" required="true" />
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="brand_image" id="add-brand-image" class="form-control" accept="image/*">

                    <div class="row d-block d-md-none">
                        <div class="col-md-3 mt-2">
                            @if (isset($brand) && $brand->image)
                            <img src="{{ asset($brand->image) }}" id="preview-brand" class="img-preview img-fluid w-50">
                            @else
                            <img src="" id="preview-brand" class="img-fluid w-50;" name="image" hidden>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="mb-3 position-relative">
                    <label for="margin" class="form-label">Margin</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="margin" value="{{ $brand->margin ?? '' }}"
                            label="Margin" placeholder="Enter Margin" min="0" max="100" />
                        <div class="input-group-prepend">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-2">
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="brand-status" class="form-control">
                        <option value="Active" {{ isset($brand) && $brand->status === 'Active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="Inactive" {{ isset($brand) && $brand->status === 'Inactive' ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                </div>
            </div>

            <div class="col-md-4 d-none d-md-block">
                @if (isset($brand) && $brand->image)
                <img src="{{ asset($brand->image) }}" id="previewBrand" class="img-preview img-fluid w-50">
                @else
                <img src="" id="previewBrand" class="img-fluid w-50" name="image" hidden>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div>
            <h4 class="card-title">Summary</h4>
            <textarea name="summary" id="summary" class="editor" rows="2">{{ $brand->summary ?? '' }}</textarea>
        </div>
        <div class="mt-3">
            <h4 class="card-title">Description</h4>
            <textarea name="description" id="description" class="editor"
                rows="2">{{ $brand->description ?? '' }}</textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">SEO</h4>
        <div class="row">
            <div class="col-sm-10 mb-2">
                <x-form-input name="heading" label="Heading" value="{{ $brand->heading ?? '' }}"
                    placeholder="Heading" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="mb-2">
                    <x-form-input name="meta_title" label="Meta title" value="{{ $brand->meta_title ?? '' }}"
                        placeholder="Meta title" />
                </div>
                <div class="mb-2">
                    <x-form-input name="meta_keywords" label="Meta Keywords" value="{{ $brand->meta_keywords ?? '' }}"
                        placeholder="Meta Keywords" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="meta_description">Meta Description</label>
                    <textarea name="meta_description" class="form-control" id="meta_description" rows="5"
                        placeholder="Meta Description">{{ $brand->meta_description ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="sticky-submit">
<button type="submit" class="btn btn-primary w-md">Submit</button>
</div>

<x-include-plugins :plugins="['image', 'contentEditor']"></x-include-plugins>
<script>
    $(function() {
        $('#add-brand-image').change(function() {
            Image(this, '#preview-brand');
            Image(this, '#previewBrand');
        });

        $('input[name="margin"]').on('input', function() {
            var value = $(this).val();
            var regex = /^(\d{1,2}(\.\d{0,2})?|100(\.0{1,2})?)$/;
            if (regex.test(value)) {
                $(this).val(value);
            } else {
                $(this).val(value.slice(0, -1));
            }
        });

        $('input[name="margin"]').on('blur', function() {
            var value = parseFloat($(this).val());

            if (value < 0 || value > 100 || isNaN(value)) {
                $(this).val('');
            }
        });
    });
</script>