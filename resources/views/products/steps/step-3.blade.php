@extends('layouts.app')

@section('title', 'Step 3')
@section('header-button')
    <a href="{{ route('products.create.step-2') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Back to previous
        step</a>
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
                            <form action="{{ route('products.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @include('products.steps.quantity-form')

                                <button type="button" class="btn btn-secondary" id="add-variant-btn">Add new
                                    variant</button>
                                <button class="btn btn-primary">Submit</button>
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

@push('scripts')
    <script>
        $(function() {
            $('form').on('keypress', function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        })
    </script>
@endpush
