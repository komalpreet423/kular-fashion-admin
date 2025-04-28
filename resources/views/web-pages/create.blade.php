@extends('layouts.app')

@section('title', 'Create a new Web Page')

@section('header-button')
    <a href="{{ route('webpages.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Go Back</a>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-error-message :message="$errors->first('message')" />
                <x-success-message :message="session('success')" />
                <form action="{{ route('webpages.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('web-pages.form')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
