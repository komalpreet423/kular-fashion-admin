@extends('layouts.app')

@section('title', 'Best Brands Overall')

@section('header-button')
<div class="d-inline-block me-2">
</div>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <x-error-message :message="session('message')" />
                <x-success-message :message="session('success')" />

                <div class="card">
                    <div class="card-body">
                        <form action="#" method="POST">
                            @csrf
                            @include('best-brands-overall.form')
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<x-include-plugins :plugins="['dataTable', 'flatpickr']"></x-include-plugins>
@endsection
