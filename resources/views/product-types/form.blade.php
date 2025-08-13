<div class="card">
    <div class="card-body">
        <h4 class="card-title">Basic Information</h4>

        <div class="row mb-2">
            <div class="col-sm-6 col-md-3">
                <div class="mb-3">
                    <label for="department_id">Department <span class="text-danger">*</span></label>
                    <select name="department_id[]" id="department_id" @class(['form-control', 'is-invalid'=>
                        $errors->has('department_id')]) multiple>
                        @foreach ($departments as $department)
                        <option value="{{$department->id}}" @selected(in_array($department->id, old('department_id',
                            $selectedDeparments ?? [])))>{{$department->name}}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                    <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="mb-3">
                    <x-form-input name="name" value="{{ $productType->name ?? '' }}" label="Product Type"
                        placeholder="Enter Product Type" required="true" />
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="mb-3">
                    <x-form-input name="short_name" value="{{ $productType->short_name ?? '' }}" label="Short Name"
                        placeholder="Enter Short Name" required="true" />
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" id="add-productType-image" class="form-control" accept="image/*">

                    <div class="row d-block d-md-none">
                        <div class="col-md-3 mt-2">
                            @if (isset($productType) && $productType->image)
                            <img src="{{ asset($productType->image) }}" id="preview-productType"
                                class="img-preview img-fluid w-50">
                            @else
                            <img src="" id="preview-productType" class="img-fluid w-50;" name="image" hidden>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="product-type-status" class="form-control">
                    <option value="Active" @selected(($productType->status ?? '') == 'Active' )>Active</option>
                    <option value="Inactive" @selected(($productType->status ?? '') == 'Inactive')>Inactive</option>
                </select>
            </div>

            <div class="col-sm-6 col-md-3"></div>
            <div class="col-sm-6 col-md-3 d-none d-md-block">
                @if (isset($productType) && $productType->image)
                <img src="{{ asset($productType->image) }}" id="preview-product-type"
                    class="img-preview img-fluid w-50">
                @else
                <img src="" id="preview-product-type" class="img-preview img-fluid w-50" name="image" hidden>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-body">
        <div>
            <h4 class="card-title">Summary</h4>
            <textarea name="summary" id="summary" class="editor" rows="2">{{ $productType->summary ?? '' }}</textarea>
        </div>
        <div class="mt-3">
            <h4 class="card-title">Description</h4>
            <textarea name="description" id="description" class="editor"
                rows="2">{{ $productType->description ?? '' }}</textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">SEO</h4>
        <div class="row">
            <div class="col-sm-10 mb-2">
                <x-form-input name="heading" label="Heading" value="{{ $productType->heading ?? '' }}"
                    placeholder="Heading" />
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="mb-2">
                    <x-form-input name="meta_title" label="Meta title" value="{{ $productType->meta_title ?? '' }}"
                        placeholder="Meta title" />
                </div>
                <div class="mb-2">
                    <x-form-input name="meta_keywords" label="Meta Keywords"
                        value="{{ $productType->meta_keywords ?? '' }}" placeholder="Meta Keywords" />
                </div>
            </div>
            <div class="col-sm-6">
                <div class="mb-3">
                    <label for="meta_description">Meta Description</label>
                    <textarea name="meta_description" class="form-control" id="meta_description" rows="5"
                        placeholder="Meta Description">{{ $productType->meta_description ?? '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>


    <div class=" sticky-submit ">
        <button type="submit" class="btn btn-primary w-md">Submit</button>
    </div>

<x-include-plugins :plugins="['select2', 'image', 'contentEditor']"></x-include-plugins>
<script>
    $(function() {
        $('#add-productType-image').change(function() {
            Image(this, '#preview-productType');
        });

        $('#preview-product-type').change(function() {
            Image(this, '#preview-product-type');
        });

        $('#department_id').select2({
            width: '100%',
        });
    });
</script>