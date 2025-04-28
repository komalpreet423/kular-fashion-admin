@extends('layouts.app', ['isVueComponent' => true])

@section('title', 'View Inventory')
@section('header-button')
    <a href="{{ route('inventory-history') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Go Back</a>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">

                <div class="row">
                    <div class="col-sm-3">
                        <h6 class="mt-1 mb-2">Sent From: <strong>{{ $inventoryTransfer->sentFrom->name }}</strong>
                        </h6>
                    </div>

                    <div class="col-sm-3">
                        <h6 class="mt-1 mb-2">Sent To: <strong>{{ $inventoryTransfer->sentTo->name }}</strong>
                        </h6>
                    </div>
                    <div class="col-sm-3">
                        <h6 class="mt-1 mb-2">Sent By: <strong>{{ $inventoryTransfer->sentBy->name }}</strong></h6>
                    </div>
                    <div class="col-sm-3">
                        <h6 class="mt-1 mb-2">Transfer Date: <strong>
                                <td>{{ date('d-m-Y', strtotime($inventoryTransfer->created_at)) }}</td>
                            </strong></h6>
                    </div>
                </div>
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />
                    <div class="card">
                        <div class="card-body">
                            <table id="invent-trans-datatable"
                                class="table table-sm table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Article Code</th>
                                        <th>Brand</th>
                                        <th>Product Type</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Short Description</th>
                                        <th>Manufacture Code</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventoryTransfer->inventoryItems as $key => $item)
                                        @for ($i = 0; $i < $item->quantity; $i++)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ $item->product->article_code ?? 'N/A' }}</td>
                                                <td>{{ $item->brand->name ?? 'N/A' }}</td>
                                                <td>{{ $item->product->productType->name ?? 'N/A' }}</td>
                                                <td>{{ $item->productColor->colorDetail->name ?? 'N/A' }}</td>
                                                <td>{{ $item->productSize->sizeDetail->size ?? 'N/A' }}</td>
                                                <td>{{ $item->product->short_description ?? 'N/A' }}</td>
                                                <td>{{ $item->product->manufacture_code ?? 'N/A' }}</td>
                                            </tr>
                                        @endfor
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <x-include-plugins :plugins="['dataTable', 'update-status']"></x-include-plugins>

    <script>
        $(document).ready(function() {

            $('#invent-trans-datatable').DataTable({

                columnDefs: [{
                    type: 'string',
                    targets: 1
                }],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function(settings) {
                    $('#invent-trans-datatable th, #invent-trans-datatable td').addClass('p-1');
                }
            });

        });
    </script>
@endsection
