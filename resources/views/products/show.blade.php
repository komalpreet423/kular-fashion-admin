@extends('layouts.app')

@section('title', 'View Article')
@section('header-button')
<a href="{{ route('products.edit.web-configuration', $product->id) }}" class="btn btn-primary"><i class="bx bx-landscape"></i> Product Web Configuration</a>
<a href="{{ route('products.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Back to products</a>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body pt-0">
                        <div class="row">
                            {{-- <div class="col-xl-3">
                                <img src="{{ asset($product->image) }}" alt="" class="img-fluid mx-auto d-block w-100 product-preview-image" onerror="this.onerror=null; this.src='{{ asset(setting('default_product_image')) }}';">
                        </div> --}}

                        <div class="col-md-12">
                            <div class="mt-4 mt-xl-3">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <h6 class="mt-1 mb-2">Article Code:
                                            <strong>{{ $product->article_code }}</strong>
                                        </h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <div>
                                            <a href="javascript: void(0);" class="text-primary">{{ $product->brand->name }}</a>
                                            > {{ $product->productType->name }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <p class="text-muted mb-2">{{ $product->short_description }}</p>
                                    </div>
                                    <div class="col-sm-4">
                                        <h6 class="mb-2">Manufacture Code: {{ $product->manufacture_code }}</h6>
                                    </div>
                                    <div class="col-sm-4">
                                        <h6>Price: <b>£{{ $product->mrp }}</b></h6>
                                    </div>
                                    @if ($product->in_date)
                                    <div class="col-sm-4">
                                        <h6>In Date: <b>{{ $product->in_date->format('d-m-Y') }}</b></h6>
                                    </div>
                                    @endif
                                    @if ($product->last_date && $product->in_date != $product->last_date)
                                    <div class="col-sm-4">
                                        <h6>Last In Date: <b>{{ $product->last_date->format('d-m-Y') }}</b>
                                        </h6>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>{{ $branches->first()->name }} Inventory</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inventoryModal">View inventory in all stores</button>
                        </div>
                        <div class="table-responsive mt-1">
                            <table class="table mb-0 table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row">Size</th>
                                        @foreach ($product->sizes as $size)
                                        <th>{{ $size->sizeDetail->size }}</th>
                                        @endforeach
                                    </tr>

                                    @foreach ($product->colors as $color)
                                    <tr>
                                        <th class="d-flex align-items-center">

                                            <h6 class="m-0">{{ $color->colorDetail->name }} ({{ $color->colorDetail->code }})</h6>

                                            {{-- <div class="me-1 d-color-code" style="background: {{ $color->colorDetail->ui_color_code }}; width: 20px; height: 20px; border-radius: 4px;">
                                            </div> --}}

                                            @if ($color->image_path)
                                            <img src="{{ asset($color->image_path) }}" alt="Color Image" class="me-2 img-thumbnail zoomable-image" style="width: 30px; height: 24px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="showFullScreenImage('{{ asset($color->image_path) }}')">
                                            @endif
                                        </th>
                                        @foreach ($product->sizes as $size)
                                        <td>{{ $size->quantity($color->id) }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="text-success fw-bold mb-0">Goods In</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row">Size</th>
                                        @foreach ($product->sizes as $size)
                                        <th>{{ $size->sizeDetail->size }}</th>
                                        @endforeach
                                    </tr>

                                    @foreach ($product->colors as $color)
                                    <tr>
                                        <th class="d-flex align-items-center">
                                            <h6 class="m-0">{{ $color->colorDetail->name }}
                                                ({{ $color->colorDetail->code }})</h6>
                                            {{-- <div class="me-1 d-color-code" style="background: {{ $color->colorDetail->ui_color_code }}"></div> --}}
                                            @if ($color->image_path)
                                            <img src="{{ asset($color->image_path) }}" alt="Color Image" class="me-2 img-thumbnail zoomable-image" style="width: 30px; height: 24px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="showFullScreenImage('{{ asset($color->image_path) }}')">
                                            @endif
                                        </th>
                                        @foreach ($product->sizes as $size)
                                        <td>{{ $size->totalQuantity($color->id) }}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach

                                    @php
                                    $mrpValues = $product->sizes->pluck('mrp');
                                    $isDifferent = $mrpValues->unique()->count() > 1;
                                    @endphp

                                    @if ($isDifferent)
                                    <tr>
                                        <th scope="row">MRP</th>
                                        @foreach ($product->sizes as $size)
                                        <td>£{{ $size->mrp }}</td>
                                        @endforeach
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>


<div class="modal fade" id="inventoryModal" tabindex="-1" aria-labelledby="inventoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventoryModalLabel">All Stores Inventory</h5>
                <div>
                    <button type="button" class="btn btn-primary btn-sm inventory-change">Color wise Inventory</button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="color-wise d-none">
                    @foreach ($product->colors as $color)
                    <div class="d-flex justify-content-center align-items-center mt-2">
                        <div class="me-1 d-color-code" style="background: {{ $color->colorDetail->ui_color_code }}"></div>
                        <h5 class="m-0">{{ $color->colorDetail->name }}
                            ({{ $color->colorDetail->code }})
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-bordered table-sm">
                            <tbody>
                                <tr>
                                    @foreach ($product->sizes as $size)
                                    <th>{{ $size->sizeDetail->size }}</th>
                                    @endforeach
                                    <th scope="row">Size</th>
                                </tr>

                                @foreach ($branches as $branch)
                                <tr>
                                    @foreach ($product->sizes as $size)
                                    <td>
                                        @if ($branch->id === 1)
                                        {{ $size->quantity($color->id) }}
                                        @else
                                        {{ $size->inventoryAvailableQuantity($color->id, $branch->id) }}
                                        @endif
                                    </td>
                                    @endforeach
                                    <th class="d-flex align-items-center">{{ $branch->name }}</th>
                                </tr>
                                @endforeach
                                <tr>
                                    @foreach ($product->sizes as $size)
                                    <td>{{ $size->totalQuantity($color->id) }}</td>
                                    @endforeach
                                    <th class="text-success">Goods In</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endforeach
                </div>
                <div class="store-wise">
                    @foreach ($branches as $branch)
                    <div class="text-center mt-2">
                        <h5 class="mb-0">{{ $branch->name }} Inventory</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <th scope="row">Size</th>
                                    @foreach ($product->sizes as $size)
                                    <th>{{ $size->sizeDetail->size }}</th>
                                    @endforeach
                                </tr>

                                @foreach ($product->colors as $color)
                                <tr>
                                    <th class="d-flex align-items-center">

                                        <h6 class="m-0">{{ $color->colorDetail->name }} ({{ $color->colorDetail->code }})</h6>
                                        {{-- <div class="me-1 d-color-code" style="background: {{ $color->colorDetail->ui_color_code }}; width: 20px; height: 20px; border-radius: 4px;"> --}}
                                        </div>

                                        @if ($color->image_path)
                                        <img src="{{ asset($color->image_path) }}" alt="Color Image" class="me-2 img-thumbnail zoomable-image" style="width: 30px; height: 24px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="showFullScreenImage('{{ asset($color->image_path) }}')">
                                        @endif
                                    </th>
                                    @foreach ($product->sizes as $size)
                                    <td>
                                        @if ($branch->id === 1)
                                            {{ $size->quantity($color->id) }}
                                        @else
                                            {{ $size->inventoryAvailableQuantity($color->id, $branch->id) }}
                                        @endif
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endforeach

                    <div class="mt-2">
                        <div class="text-center">
                            <h5 class="text-success fw-bold mb-0">Goods In</h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table mb-0 table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <th scope="row">Size</th>
                                        @foreach ($product->sizes as $size)
                                        <th>{{ $size->sizeDetail->size }}</th>
                                        @endforeach
                                    </tr>

                                    @foreach ($product->colors as $color)
                                    <tr>
                                        <th class="d-flex align-items-center">
                                            <h6 class="m-0">{{ $color->colorDetail->name }}
                                                ({{ $color->colorDetail->code }})</h6>
                                            {{-- <div class="me-1 d-color-code" style="background: {{ $color->colorDetail->ui_color_code }}"> --}}
                                            </div>
                                            @if ($color->image_path)
                                            <img src="{{ asset($color->image_path) }}" alt="Color Image" class="me-2 img-thumbnail zoomable-image" style="width: 30px; height: 24px; object-fit: cover; border-radius: 4px; cursor: pointer;" onclick="showFullScreenImage('{{ asset($color->image_path) }}')">
                                            @endif
                                        </th>

                                        @foreach ($product->sizes as $size)
                                        <td>{{ $size->totalQuantity($color->id) }}</td>
                                        @endforeach
                                        
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<style>
    .fullscreen-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .fullscreen-image {
        max-width: 90%;
        max-height: 90%;
        cursor: zoom-in;
        transition: transform 0.3s ease-in-out;
    }

</style>

@push('scripts')
<script>
    $(function() {
        $('.inventory-change').click(function() {
            if ($('.store-wise').hasClass('d-none')) {
                $(this).html('Color Wise Inventory');
            } else {
                $(this).html('Store Wise Inventory');
            }
            $('.store-wise').toggleClass('d-none');
            $('.color-wise').toggleClass('d-none');
        })

    })

    function showFullScreenImage(imageUrl) {
        let fullScreenDiv = $(`
            <div class="fullscreen-overlay">
                <img src="${imageUrl}" class="fullscreen-image" />
            </div>
        `);

        $('body').append(fullScreenDiv);

        $('.fullscreen-image').on('click', function() {
            if ($(this).css('transform') === 'matrix(1, 0, 0, 1, 0, 0)') {
                $(this).css({
                    'transform': 'scale(1.5)'
                    , 'cursor': 'zoom-out'
                });
            } else {
                $(this).css({
                    'transform': 'scale(1)'
                    , 'cursor': 'zoom-in'
                });
            }
        });

        $('.fullscreen-overlay').on('click', function(e) {
            if (!$(e.target).hasClass('fullscreen-image')) {
                $(this).remove();
            }
        });
    }

</script>
@endpush
@endsection
