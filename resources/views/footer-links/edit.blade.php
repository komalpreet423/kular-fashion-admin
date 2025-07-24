@extends('layouts.app')

@section('title', 'Edit Footer Link')

@section('header-button')
    <a href="{{ route('footer-links.index') }}" class="btn btn-primary">
        <i class="bx bx-arrow-back"></i> Go Back
    </a>
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <x-error-message :message="$errors->first('message')" />
            <x-success-message :message="session('success')" />
            <div class="card">
                <form action="{{ route('footer-links.update', $block) }}" method="POST" enctype="multipart/form-data" id="blockForm">
                    @csrf
                    @method('PUT')
                    @include('footer-links.form')
                </form>
            </div>
        </div>
    </div>
@endsection
