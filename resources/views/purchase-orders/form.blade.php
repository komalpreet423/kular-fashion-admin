<div class="row mb-2">
    <div class="col-sm-6 col-md-2 mb-3">
        <x-form-input name="supplier_order_no" value="{{ $purchaseOrder->order_no ?? '' }}" label="Supplier Order No" placeholder="Supplier Order No"
            required="true" />
    </div>
    <div class="col-sm-6 col-md-2 mb-3">
        <x-form-input name="supplier_order_date" class="date-picker" value="{{ $purchaseOrder->supplier_order_date ?? '' }}" label="Supplier Order Date"
            placeholder="Supplier Order Date" required="true" />
    </div>
    <div class="col-sm-6 col-md-2 mb-3">
        <x-form-input name="delivery_date" class="date-picker" label="Delivery Date" placeholder="Delivery Date" value="{{ $purchaseOrder->delivery_date ?? '' }}"
            required="true" />
    </div>

    <div class="col-sm-6 col-md-2 mb-3">
        <label for="supplier">Supplier Name <span class="text-danger">*</span></label>
        <select name="supplier" id="supplier" @class(['form-control', 'is-invalid' => $errors->has('supplier')])>
            <option value="" disabled selected>Select Supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}"  @selected(old('supplier', isset($purchaseOrder) ? $purchaseOrder->supplier_id : '') == $supplier->id)>{{ $supplier->supplier_name }}</option>
            @endforeach
        </select>
        @error('supplier')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>
</div>

<div id="product-fields-container">
    @forelse($purchaseOrder->purchaseOrderProduct ?? [] as $index => $product)
    <div class="product-field-group mb-3 border p-3" data-product-index="{{$index}}">
        <div class="row">
            <div class="col-sm-6 col-md-2 mb-3">
                <x-form-input name="products[{{ $index }}][product_code]" label="Product Code" placeholder="Enter Product Code" value="{{ old('products.' . $index . '.product_code', $product->product_code) }}" required />
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <label for="product_type">Product Type<span class="text-danger">*</span></label>
                <select name="products[{{ $index }}][product_type]" class="form-control @error('products.' . $index . '.product_type') is-invalid @enderror">
                    <option value="" disabled selected>Select Product Type</option>
                    @foreach ($productTypes as $productType)
                        <option value="{{ $productType->id }}" @selected(old('products.' . $index . '.product_type', $product->product_type_id ?? '') == $productType->id)>{{ $productType->name }}</option>
                    @endforeach
                </select>
                @error('products.' . $index . '.product_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <label for="size_scale">Size Scale<span class="text-danger">*</span></label>
                <select name="products[{{ $index }}][size_scale]" class="form-control size-scale-dropdown @error('products.' . $index . '.size_scale') is-invalid @enderror">
                    <option value="" disabled selected>Select Size Scale</option>
                    @foreach ($sizeScales as $sizeScale)
                        <option value="{{ $sizeScale->id }}" @selected(old('products.' . $index . '.size_scale', $product->size_scale_id ?? '') == $sizeScale->id)>
                            {{ $sizeScale->name }}
                            @if (isset($sizeScale->sizes))
                                ({{ $sizeScale->sizes->first()->size }} - {{ $sizeScale->sizes->last()->size }})
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('products.' . $index . '.size_scale')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 col-md-2 mb-3">
                <label for="min_size_id">Min Size<span class="text-danger">*</span></label>
                <select name="products[{{ $index }}][min_size]" class="form-control min-size-dropdown @error('products.' . $index . '.min_size') is-invalid @enderror">
                    <option value="" disabled selected>Select Min Size</option>
                    @foreach ($sizes->where('size_scale_id', $product->size_scale_id) as $size)
                        <option value="{{ $size->id }}" @selected(old('products.' . $index . '.min_size', $product->min_size_id ?? '') == $size->id)>
                            {{ $size->size }}
                        </option>
                    @endforeach
                </select>
                @error('products.' . $index . '.min_size')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <label for="max_size_id">Max Size<span class="text-danger">*</span></label>
                <select name="products[{{ $index }}][max_size]" class="form-control max-size-dropdown @error('products.' . $index . '.max_size') is-invalid @enderror">
                    <option value="" disabled selected>Select Max Size</option>
                    @foreach ($sizes->where('size_scale_id', $product->size_scale_id) as $size)
                        <option value="{{ $size->id }}" @selected(old('products.' . $index . '.max_size', $product->max_size_id ?? '') == $size->id)>
                            {{ $size->size }}
                        </option>
                    @endforeach
                </select>
                @error('products.' . $index . '.max_size')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 col-md-2">
                <x-form-input name="products[{{ $index }}][delivery_date]" label="Delivery Date" class="date-picker" placeholder="Enter Delivery Date" value="{{ old('products.' . $index . '.delivery_date', $product->delivery_date) }}" required />
            </div>

            <div class="col-sm-6 col-md-2 mb-3">
                <x-form-input name="products[{{ $index }}][price]" label="Price" placeholder="Enter Price" value="{{ old('products.' . $index . '.price', $product->price) }}" required />
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <x-form-input name="products[{{ $index }}][short_description]" label="Short Description" placeholder="Enter Short Description" value="{{ old('products.' . $index . '.short_description', $product->short_description) }}" required />
            </div>

            <div class="col-sm-6 col-md-3 mt-4">
                <button type="button" class="btn btn-primary add-product-variant"  data-toggle="modal" data-target="#variantModal">
                    <i class="fas fa-plus"></i> Variant
                </button>
                {{--<button type="button" class="btn btn-secondary copy-product" disabled><i class="fas fa-copy"></i></button>--}}
                <button type="button" class="btn btn-danger remove-product-field"><i class="fas fa-trash-alt"></i></button>
            </div>
        </div>
        <div class="variants-container">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Size</th>
                        @php
                            $sizes = collect();
                            foreach($purchaseOrder->purchaseOrderProduct as $purchaseOrderProduct) {
                                foreach($purchaseOrderProduct->variants as $variants) {
                                    foreach($variants->sizes as $size) {
                                        $sizes->push($size->sizeDetail->size);
                                    }
                                }
                            }
                            $sizes = $sizes->unique()->values();
                        @endphp
                    
                        @foreach($sizes as $size)
                            <th>{{ $size }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>    
                    @foreach($purchaseOrder->purchaseOrderProduct as $proKey => $purchaseOrderProduct)
                        @foreach($purchaseOrderProduct->variants as $vKey => $variants)
                        <tr>
                            <td>{{$variants->colors->name}}</td>
                            <td class="color-code" hidden="">
                                <input type="hidden" name="products[{{$proKey}}][variants][{{$vKey}}][supplier_color_code]" value="{{$variants->supplier_color_code}}">
                            </td>
                            <td class="color-name" hidden="">
                                <input type="hidden" name="products[{{$proKey}}][variants][{{$vKey}}][supplier_color_name]" value="{{$variants->supplier_color_name}}">
                            </td>
                            <td class="color-id" hidden="">
                                <input type="hidden" name="products[{{$proKey}}][variants][{{$vKey}}][color_id]" value="{{$variants->color_id}}">
                            </td>
                            @foreach($variants->sizes as $sizes)
                                <td><input type="number" name="products[{{$proKey}}][variants][{{$vKey}}][size][{{$sizes->size_id}}]" value="{{$sizes->quantity}}" class="form-control"></td>
                            @endforeach
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <!-- Empty product form for adding new products -->
    <div class="product-field-group mb-3 border p-3" data-product-index="0">
        <div class="row">
            <div class="col-sm-6 col-md-2 mb-3">
                <x-form-input name="products[0][product_code]" label="Product Code" placeholder="Enter Product Code" value=""
                    required />
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <label for="product_type">Product Type<span class="text-danger">*</span></label>
                <select name="products[0][product_type]" @class([
                    'form-control',
                    'is-invalid' => $errors->has('products.0.product_type'),
                ])>
                    <option value="" disabled selected>Select Product Type</option>
                    @foreach ($productTypes as $productType)
                        <option value="{{ $productType->id }}" @selected(old('products.0.product_type', $product->product_type ?? '') == $productType->id)>
                            {{ $productType->name }}</option>
                    @endforeach
                </select>

                @error('products.0.product_type')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <label for="size_scale">Size Scale<span class="text-danger">*</span></label>
                <select name="products[0][size_scale]" id="size_scale" @class([
                    'form-control size-scale-dropdown',
                    'is-invalid' => $errors->has('products.0.size_scale'),
                ])>
                    <option value="" disabled selected>Select size scale</option>
                    @foreach ($sizeScales as $sizeScale)
                        <option value="{{ $sizeScale->id }}" @selected(old('products.0.size_scale', $product->size_scale_id ?? '') == $sizeScale->id)>
                            {{ $sizeScale->name }}

                            @if (isset($sizeScale->sizes))
                                ({{ $sizeScale->sizes->first()->size }} -
                                {{ $sizeScale->sizes->last()->size }})
                            @endif
                        </option>
                    @endforeach
                </select>

                @error('products.0.size_scale')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-sm-6 col-md-2 mb-3">
                <label for="min_size_id">Min Size<span class="text-danger">*</span></label>
                <select name="products[0][min_size]" @class([
                    'form-control min-size-dropdown',
                    'is-invalid' => $errors->has('products.0.min_size'),
                ])>
                    <option value="" disabled selected>Select Min Size</option>
                </select>

                @error('products.0.min_size')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <label for="max_size_id">Max Size<span class="text-danger">*</span></label>
                <select name="products[0][max_size]" @class([
                    'form-control max-size-dropdown',
                    'is-invalid' => $errors->has('products.0.max_size'),
                ])>
                    <option value="" disabled selected>Select Max Size</option>
                </select>

                @error('products.0.max_size')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="col-sm-6 col-md-2">
                <x-form-input name="products[0][delivery_date]" label="Delivery Date" class="date-picker"
                    placeholder="Enter Delivery Date" required />
            </div>
            <div class="col-sm-6 col-md-2 mb-3">
                <x-form-input name="products[0][price]" label="Price" placeholder="Enter Price" required />
            </div>
            <div class="col-sm-6 col-md-3 mb-3">
                <x-form-input name="products[0][short_description]" label="Short Description"
                    placeholder="Enter Short Description" required />
            </div>
            <div class="col-sm-6 col-md-3 mt-4">
                <button type="button" class="btn btn-primary add-product-variant" disabled data-toggle="modal"
                    data-target="#variantModal"><i class="fas fa-plus"></i>
                    Variant</button>
                {{--<button type="button" class="btn btn-secondary copy-product" disabled><i class="fas fa-copy"></i></button>--}}
                <button type="button" class="btn btn-danger remove-product-field"><i
                        class="fas fa-trash-alt"></i></button>
            </div>
        </div>

        <div class="variants-container">
        </div>
    </div>
    @endforelse
</div>
<button type="button" id="add-product-field" class="btn btn-primary">Add New Product</button>