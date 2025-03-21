@extends('layouts.app')

@section('title', 'Edit variations for article '.$product->article_code)
@section('header-button')
    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Back to previous step</a>
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
                            <form action="{{ route('products.update', $product->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                @include('products.steps.quantity-form')

                                <button type="button" class="btn btn-secondary me-2" id="add-variant-btn">Add new variant</button>
                                <button class="btn btn-primary">Save</button>
                            </form>    
                            
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('products.steps.quantity-form-script')
    <x-include-plugins :plugins="['select2', 'imagePreview']"></x-include-plugins>
@endsection