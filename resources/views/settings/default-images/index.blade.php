@extends('layouts.app')

@section('title', 'Default Images')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <form class="form-horizontal" action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @php
                                    $images = [
                                        'default_department_image'    => 'Default Department Image',
                                        'default_product_type_image'  => 'Default Product Type Image',
                                        'default_brand_image'         => 'Default Brand Image',
                                        'default_product_image'       => 'Default Product Image'
                                    ];
                                @endphp

                                <div class="row">
                                    @foreach ($images as $key => $label)
                                        <div class="col-sm-6 col-md-4 col-lg-4 col-xl-3">
                                            <div class="mb-3">
                                                <x-form-input type="file" name="{{ $key }}" id="add-{{ $key }}" class="form-control" label="{{ $label }}" onchange="previewImage(event, '{{ $key }}')" accept="image/*" />
                                                
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            @if (setting($key))
                                                                <img src="{{ asset(setting($key)) }}" id="preview-{{ $key }}"
                                                                class="img-preview img-fluid mt-2">
                                                                @else
                                                                <img src="" id="preview-{{ $key }}" class="img-fluid mt-2 w-150" hidden>
                                                            @endif
                                                        </div>
                                                    </div>
                                            </div>
                                        </div>
                                    @endforeach
                                <div>
                                <button type="submit" class="btn btn-primary w-md">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-include-plugins :plugins="['imagePreview']"></x-include-plugins>
@endsection