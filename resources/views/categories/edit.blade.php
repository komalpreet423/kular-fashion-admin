@extends('layouts.app')

@section('title', 'Edit Category')
@section('header-button')
    <a href="{{ route('categories.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Back to all categories</a>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <form action="{{ route('categories.update', $category->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('categories.form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
