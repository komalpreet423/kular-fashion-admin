@extends('layouts.app')

@section('title', 'Create block')

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
            <form action="{{ route('footer-links.store') }}" method="POST" id="blockForm">
                @csrf
                @include('footer-links.form')
            </form>
        </div>
    </div>
</div>
@endsection