@extends('layouts.app')

@section('title', 'Categories')
@section('header-button')
    <a href="{{ url('download-category-sample') }}" class="btn btn-primary primary-btn btn-md me-2"><i class="bx bx-download"></i>
        Download Brands Sample </a>
    <div class="d-inline-block me-2">
        {{-- <form id="importForm" action="{{ route('import.categories') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="fileInput" class="btn btn-primary primary-btn btn-md mb-0">
                <i class="bx bx-cloud-download"></i> Import Brands
                <input type="file" id="fileInput" name="file" accept=".csv, .xlsx" style="display:none;">
            </label>
        </form> --}}
    </div>
    {{-- @if (Auth::user()->can('create categories')) --}}
        <a href="{{ route('categories.create') }}" class="btn btn-primary"><i class="bx bx-plus"></i> Add
            New Category</a>
    {{-- @endif --}}
@endsection

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />
                    @if (session('import_errors'))
                        <div class="alert alert-danger">
                            <ul>
                                @foreach (session('import_errors') as $error)
                                    <li>{{ $error['message'] }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="card">
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category Name</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        {{-- @canany(['edit categories', 'delete categories']) --}}
                                            <th>Action</th>
                                        {{-- @endcanany --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($categories as $key => $category)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td><img src="{{ asset($category->image) }}" width="50" height="30"
                                                    onerror="this.onerror=null; this.src='{{ asset(setting('default_brand_image')) }}';">
                                            </td>
                                            <td>
                                                <input type="checkbox" id="{{ $category->id }}" class="update-status"
                                                    data-id="{{ $category->id }}" switch="success" data-on="Active"
                                                    data-off="Inactive" {{ $category->status === 'Active' ? 'checked' : '' }}
                                                    data-endpoint="{{ route('categories.updateStatus') }}" />
                                                <label class="mb-0" for="{{ $category->id }}" data-on-label="Active"
                                                    data-off-label="Inactive"></label>
                                            </td>
                                            {{-- @canany(['edit categories', 'delete categories']) --}}
                                                <td>
                                                    {{-- @if (Auth::user()->can('edit categories')) --}}
                                                        <a href="{{ route('categories.edit', $category->id) }}"
                                                            class="btn btn-primary btn-sm edit py-0 px-1"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                    {{-- @endif --}}
                                                    {{-- @if (Auth::user()->can('delete categories')) --}}
                                                        <button data-source="Brand"
                                                            data-endpoint="{{ route('categories.destroy', $category->id) }}"
                                                            class="delete-btn btn btn-danger btn-sm edit py-0 px-1">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    {{-- @endif --}}
                                                </td>
                                            {{-- @endcanany --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-include-plugins :plugins="['dataTable', 'update-status']"></x-include-plugins>
    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                columnDefs: [{
                    type: 'string',
                    targets: 1
                }],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function(settings) {
                    $('#datatable th, #datatable td').addClass('p-1');
                },
            });

            $('#importButton').on('click', function() {
                $('#fileInput').click();
            });

            $('#fileInput').on('change', function(event) {
                var file = $(this).prop('files')[0];
                if (file) {
                    var fileType = file.type;
                    if (fileType === 'text/csv' || fileType ===
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                        $('#importForm').submit();
                    } else {
                        alert('Please select a valid CSV or XLSX file.');
                    }
                }
            });
        });
    </script>
@endsection
