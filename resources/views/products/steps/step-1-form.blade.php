<div class="row mb-2">
    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="article_code" id="article_code" value="{{ $product->article_code ?? '' }}"
                label="Article Code" placeholder="Enter Article Code" required="true" readonly="true" />
        </div>
    </div>
    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="manufacture_code" value="{{ $product->manufacture_code ?? request()->get('mfg_code') }}"
                label="Manufacture Code" placeholder="Enter Manufacture Code" required="true"
                readonly="{{ $isEditing ?? false }}"/>
                <span id="manufacture_err_code" style="color:red; display:none;"></span>    
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="productType">Product Type <span class="text-danger">*</span></label>
            <select name="product_type_id" id="product_type" @disabled($isEditing ?? false) @class([
                'productType form-control',
                'is-invalid' => $errors->has('product_type_id'),
            ])>
                <option value="" disabled>Select Product Type</option>
                @if (isset($productTypes))
                    @foreach ($productTypes as $productType)
                        <option value="{{ $productType->id }}" @selected(old('product_type_id', $product->product_type_id ?? '') == $productType->id)>
                            {{ $productType->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('product_type_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="brand_id">Brand <span class="text-danger">*</span></label>
            <select name="brand_id" id="brand_id" @disabled($isEditing ?? false)
                @class(["form-control", 'is-invalid' => $errors->has('brand_id')])>
                <option value="" disabled selected>Select brand</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}" data-margin="{{ $brand->margin }}"
                        @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
        <input type="hidden" id="brand_margin" value="{{ $product->brand->margin ?? 50 }}">
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="mrp" type="number" step="0.01" value="{{ $product->mrp ?? '' }}" label="MRP"
                placeholder="Enter MRP" required="true" />
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="supplier_price" type="number" step="0.01"
                value="{{ $product->supplier_price ?? '' }}" label="Supplier Price" placeholder="Enter Supplier Price"
                required="true" />
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="size_scale_id">Size Scale<span class="text-danger">*</span></label>
            <select name="size_scale_id" @disabled($isEditing ?? false) id="size_scale_id" @class([
                'form-control',
                'is-invalid' => $errors->has('size_scale_id'),
            ])>
                <option value="" disabled>Select size scale</option>
                @foreach ($sizeScales as $sizeScale)
                    @php
                        $isSelected = false;
                    @endphp

                    @if (!old('size_scale_id', $product->size_scale_id ?? '') && $sizeScale->is_default)
                        @php
                            $isSelected = true;
                        @endphp
                    @elseif(old('size_scale_id', $product->size_scale_id ?? '') == $sizeScale->id)
                        @php
                            $isSelected = true;
                        @endphp
                    @endif

                    <option value="{{ $sizeScale->id }}" @disabled(count($sizeScale->sizes) === 0) @selected($isSelected)>
                        {{ $sizeScale->name }}
                        @if (count($sizeScale->sizes) > 0)
                            ({{ $sizeScale->sizes->first()->size }} - {{ $sizeScale->sizes->last()->size }})
                        @endif
                    </option>
                @endforeach
            </select>
            @error('size_scale_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6 col-md-4">
        <div class="mb-3">
            <x-form-input name="short_description" value="{{ $product->short_description ?? '' }}"
                label="Short Description" placeholder="Enter Short Description" required="true" maxlength="22" />
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="tax_id">Tax</label>
            <select name="tax_id" id="tax_id" @class(['form-control', 'is-invalid' => $errors->has('tax_id')])>
                <option value="" disabled>Select Tax</option>
                @foreach ($taxes as $tax)
                    <option value="{{ $tax->id }}" @selected(isset($product->tax_id) && $product->tax_id == $tax->id) @selected(!isset($product->tax_id) && $tax->is_default == 1)>
                        {{ $tax->tax }}%
                    </option>
                @endforeach
            </select>
            @error('tax_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="in_date" class="date-picker" :disabled="isset($isEditing) ? $isEditing : false"
                value="{{ isset($product->in_date) && $product->in_date ? \Carbon\Carbon::parse($product->in_date)->format('d-m-Y') : now()->format('d-m-Y') }}"
                label="In Date" placeholder="Enter In Date" readonly="true" />
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="last_date" class="date-picker" :disabled="isset($isEditing) ? $isEditing : false"
                value="{{ now()->format('d-m-Y') }}" label="Last In Date" placeholder="Enter Last In Date" />
        </div>
    </div>
    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="status" class="form-label">Season</label>
            <select name="season" id="season" @disabled($isEditing ?? false) @class(['form-control', 'is-invalid' => $errors->has('season')])>
                <option value="Summer" @selected(($product->season ?? setting('default_season')) === 'Summer')>Summer
                </option>
                <option value="Winter" @selected(($product->season ?? setting('default_season')) === 'Winter')>Winter
                </option>
                <option value="Autumn" @selected(($product->season ?? setting('default_season')) === 'Autumn')>Autumn
                </option>
                <option value="Spring" @selected(($product->season ?? setting('default_season')) === 'Spring')>Spring
                </option>
            </select>
            @error('season')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="product-status" class="form-label">Status</label>
            <select name="status" id="product-status" class="form-control">
                <option value="Active" @selected(($product->status ?? '') === 'Active')>Active</option>
                <option value="Inactive" @selected(($product->status ?? '') === 'Inactive')>Inactive</option>
            </select>
        </div>
    </div>

    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="department_id">Department<span class="text-danger">*</span></label>
            <select name="department_id" id="department_id" @disabled($isEditing ?? false) @class([
                'form-control',
                'is-invalid' => $errors->has('department_id'),
            ])>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected(($product->department_id ?? '') == $department->id)>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
            @error('department_id')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    @php
        $selectedTags = old('tags', $productTags ?? []);
    @endphp
    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <label for="tags">Tags</label>
            <select name="tags[]" id="tags" @class(['form-control', 'is-invalid' => $errors->has('tags')]) multiple>
                <option value="" disabled>Select tag</option>
                @foreach ($tags as $tag)
                    <option value="{{ $tag->id }}" @selected(in_array($tag->id, $selectedTags))>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
            @error('tags')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col-sm-6 col-md-2">
        <div class="mb-3">
            <x-form-input name="supplier_ref" value="{{ $product->supplier_ref ?? '' }}"
                readonly="{{ $isEditing ?? false }}" label="Supplier Ref" placeholder="Enter Supplier Ref" />
        </div>
    </div>
</div>

<div class="row mb-2">
    <h5>Product Web Basic Info</h5>
    <div class="col-md-4">
        <x-form-input name="name" value="{{ $product->name ?? '' }}" label="Product Name"
            placeholder="Enter Product Name" required="true" />
    </div>
    <div class="col-md-2">
        <x-form-input name="price" type="number" step="0.01" value="{{ $product->price ?? '' }}"
            label="Web Price" placeholder="Enter Web Price" required="true" />
    </div>
    <div class="col-md-2">
        <x-form-input name="sale_price" type="number" step="0.01" value="{{ $product->sale_price ?? '' }}"
            label="Sale Price" placeholder="Enter Sale Price" />
    </div>
    <div class="col-md-2">
        <x-form-input name="sale_start" class="sale-date-picker"
            value="{{ isset($product->sale_start) && $product->sale_start ? \Carbon\Carbon::parse($product->sale_start)->format('d-m-Y') : '' }}"
            label="Sale Start at" placeholder="Sale Start at" />
    </div>
    <div class="col-md-2">
        <x-form-input name="sale_end" class="sale-date-picker"
            value="{{ isset($product->sale_end) && $product->sale_end ? \Carbon\Carbon::parse($product->sale_end)->format('d-m-Y') : '' }}"
            label="Sale End at" placeholder="Sale End at" />
    </div>
</div>

<div class="row mb-2">
    <div class="col-lg-6 mb-2">
        <button type="submit" class="btn btn-primary w-md" id="first-submit">Continue</button>
    </div>
</div>

<x-include-plugins :plugins="['select2', 'image', 'datePicker']"></x-include-plugins>
@push('scripts')
    <script>
        $(document).on('keyup','#manufacture_code',function(){
            var mfgCode = $(this).val();
            $.ajax({
                url : `{{ route('products.check-mfgcode') }}/`+mfgCode,
                type : 'GET',
                success : function(resp){
                    if(resp.success){
                        $('#manufacture_err_code').text(resp.message).show();
                        $('#first-submit').prop('disabled',true);
                    }else{
                        $('#manufacture_err_code').hide();
                        $('#first-submit').prop('disabled',false);
                    }
                },error : function(err){
                    console.log(err);
                }
            });
        });
        $(function() {
            flatpickr('.sale-date-picker', {
                dateFormat: "d-m-Y",
                allowInput: true,
                minDate: "today"
            });

            $('#image').change(function() {
                Image(this, '#preview-product');
            });

            $('form').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        });

        function refreshProductTypeDropdown(departmentId) {
            const productTypeSelect = $('#product_type');

            productTypeSelect.html('<option value="" disabled selected>Select Product Type</option>');

            if (departmentId) {
                $.ajax({
                    url: `/get-product-type/${departmentId}`,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data && data.length > 0) {
                            data.forEach(function(item) {
                                if (item.product_types) {
                                    const option = new Option(item.product_types.name, item
                                        .product_type_id);
                                    productTypeSelect.append(option);
                                }
                            });
                        }

                        const selectedProductTypeId =
                            "{{ old('product_type_id', $product->product_type_id ?? '') }}";
                        if (selectedProductTypeId) {
                            productTypeSelect.val(selectedProductTypeId).trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching product types:', error);
                    }
                });
            } else {
                productTypeSelect.select2("destroy").html(
                    '<option value="" disabled selected>Select Product Type</option>');
                productTypeSelect.select2({
                    width: '100%',
                    placeholder: 'Select Product Type',
                    allowClear: true
                });
            }
        }

        $(document).ready(function() {
            $('#product_type, #department_id, #season, #brand_id, #tags, #size_scale_id').select2({
                width: '100%',
            });

            @if (empty($product->article_code))
                let lastCode = parseInt("{{ $latestNewCode ?? '300000' }}");
                let articleCode = lastCode + 1;
                $('#article_code').val(String(articleCode).padStart(6, '0'));
            @endif

            $('#department_id').change(function() {
                const departmentId = $(this).val();
                refreshProductTypeDropdown(departmentId);
            });

            function updateSupplierPrice() {
                var margin = parseFloat($('#brand_margin').val());
                var mrp = parseFloat($('#mrp').val());

                if (!isNaN(mrp) && margin >= 0) {
                    var supplierPrice = mrp * (1 - margin / 100);
                    $('#supplier_price').val(supplierPrice.toFixed(2));
                } else {
                    $('#supplier_price').val('');
                }
            }

            $('#brand_id').change(function() {
                var selectedBrand = $(this).find('option:selected');
                var margin = selectedBrand.data('margin');

                $('#brand_margin').val(margin);
                updateSupplierPrice();
            });

            $('#mrp').on('input', function() {
                $('[name="price"]').val($(this).val());
                updateSupplierPrice();
            });

            if ($('#brand_id').val()) {
                updateSupplierPrice();
            }

            $('#short_description').on('input', function() {
                $('[name="name"]').val($(this).val());
            });

            var departmentId = $('#department_id').val();
            refreshProductTypeDropdown(departmentId);
        });
    </script>
@endpush
