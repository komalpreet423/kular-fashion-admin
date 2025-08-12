@extends('layouts.app')

@section('title', 'Products')
@section('header-button')

<form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" id="importForm" class="d-inline">
@csrf
<input type="file" name="file" id="fileInput"  accept=".csv, .xls, .xlsx" required style="display: none;" onchange="document.getElementById('importForm').submit();">
<button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click();">
    <i class="fas fa-file-import"></i> Import Products
</button>
</form>

{{-- <a href="{{ route('products.export') }}" class="btn btn-primary">
    <i class="bx bx-download"></i> Download Product Configuration File
</a> --}}

<button id="bulk-edit-button" class="btn btn-warning d-none" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
    <i class="fas fa-edit"></i> Bulk Edit
</button>

@if (Auth::user()->can('create products'))
<a href="{{ route('products.create') }}" id="add-product-link" class="btn btn-primary">
    <i class="bx bx-plus fs-16"></i> Add New Product
</a>
@endif
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
                        <div class="row">
                            <div class="form-group col-3 mb-2">
                                <!--label for="brandFilter" class="mb-0">Brand Name:</label-->
                                <select id="brandFilter" class="form-control select2">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-3 mb-2">
                                <!--label for="typeFilter" class="mb-0">Product Type:</label-->
                                <select id="typeFilter" class="form-control select2">
                                    <option value="">All Products Types</option>
                                    @foreach ($productTypes as $productType)
                                    <option value="{{ $productType->id }}">{{ $productType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-3 mb-2">
                                <!--label for="departmentFilter" class="mb-0">Department:</label-->
                                <select id="departmentFilter" class="form-control select2">
                                    <option value="">All Department</option>
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-3 mb-2">
                                <!--label for="departmentFilter" class="mb-0">Department:</label-->
                                <input type="text" id="custom-search-input" class="form-control w-100" placeholder="Search Products">
                            </div>
                        </div>
                        <div class="row action-section mt-1 d-none" id="action-section">
                            <h6>Action for selected products</h6>
                            <div class="form-group col-3 mb-2">
                                <select id="bulkEditVisible" class="form-control">
                                    <option disabled selected>Assign Visibilty</option>
                                    <option value="0">In-Active</option>
                                    <option value="1">Active</option>
                                    <option value="2">Hide When Out of Stock</option>
                                </select>
                            </div>
                            @php
                                $selectedCategories = old('category_parent_id', $productCategories ?? []);
                            @endphp
                            <div class="form-group col-3 mb-2">
                                <select class="form-control" id="category" name="as_parent_id" multiple>
                                    @include('products.partials.category-dropdown', [
                                        'categories' => $categories,
                                        'prefix' => '',
                                        'selectedCategories' => $selectedCategories
                                    ])
                                </select>
                            </div>
                            <div class="form-group col-3 mb-2">
                                <select class="form-control" id="un-category" name="un_parent_id" multiple>
                                    @include('products.partials.category-dropdown', [
                                        'categories' => $categories,
                                        'prefix' => '',
                                        'selectedCategories' => $selectedCategories
                                    ])
                                </select>
                            </div>
                        </div>
                        <table id="product-table" data-selected-products="" data-unselected-products=""
                            class="table table-bordered dt-responsive nowrap w-100 table-striped">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                                    </th>
                                    <th>#</th>
                                    <th>Article Code</th>
                                    <th>Product Type</th>
                                    <th>Brand</th>
                                    <th>Department</th>
                                    <th>Description</th>
                                    <th>Manufacture Code</th>
                                    <th>Price</th>
                                    <th>Visible</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<x-include-plugins :plugins="['dataTable', 'update-status', 'select2']"></x-include-plugins>


@include('products.partials.bulk-edit')
@include('products.script')

@push('styles')
<style>
    #product-table_filter label {
        display: none;
    }

    #product-table_wrapper #custom-search-input {
        display: none;
    }

  
</style>
@endpush
@endsection