@extends('layouts.app')

@section('title', 'Edit a Purchase Order')
@section('header-button')
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Back to purchase
        orders</a>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @error('products')
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @enderror

                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('purchase-orders.store') }}" method="post"
                                enctype="multipart/form-data">
                                @csrf

                                @include('purchase-orders.form')
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-include-plugins :plugins="['select2', 'datePicker', 'jQueryValidate']"></x-include-plugins>
    @include('purchase-orders.create-order-script')
@endsection