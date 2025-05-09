@extends('layouts.app')

@section('title', 'Create a new product type')
@section('header-button')
    @if (Auth::user()->can('create product types'))
        <a href="{{ route('product-types.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Go Back</a>
    @endif
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="session('message')" />
                    <x-success-message :message="session('success')" />
                    <form action="{{ route('product-types.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @include('product-types.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
