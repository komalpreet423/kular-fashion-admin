@extends('layouts.app')

@section('title', 'Purchase Orders')
@section('header-button')
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Create a new Order</a>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-striped dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="p-1">#</th>
                                        <th class="p-1">Order ID</th>
                                        <th class="p-1">Supplier Name</th>
                                        <th class="p-1">Order Date</th>
                                        <th class="p-1">Delivery Date</th>
                                            <th class="p-1">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($purchaseOrders as $key => $purchaseOrder)
                                    <tr>
                                        <td>{{ $key+1 }}</td>
                                        <td>{{ $purchaseOrder->order_no }}</td>
                                        <td>{{ $purchaseOrder->supplier->supplier_name}}</td>
                                        <td>{{ $purchaseOrder->supplier_order_date }}</td>
                                        <td>{{ $purchaseOrder->delivery_date }}</td>
                                        <td><a href="{{ route('purchase-orders.edit', $purchaseOrder->id) }}" class="btn btn-primary btn-sm edit py-0 px-1"><i class="fas fa-pencil-alt"></i></a>
                                            <button data-source="Color" data-endpoint="{{ route('colors.destroy', $purchaseOrder->id) }}" class="delete-btn btn btn-danger btn-sm edit py-0 px-1"><i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <x-include-plugins :plugins="['dataTable']"></x-include-plugins>
@endsection
