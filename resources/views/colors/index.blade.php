@extends('layouts.app')

@section('title', 'Colors')
@section('header-button')
    @if (Auth::user()->can('create colors'))
        <a href="{{ route('colors.create') }}" class="btn btn-primary">Add New Color</a>
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
                            <table id="datatable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Color Name</th>
                                        <th>Color Short Code</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        @canany(['edit colors', 'delete colors'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($colors as $key => $color)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ ucwords($color->name) }}</td>
                                            <td>{{ $color->code }}</td>
                                            <th><a href=" {{  route('index.related-products',['colors', $color->id]) }}">{{ $color->product_color_count }}</a></th>
                                            <td>
                                                <input type="checkbox" id="{{ $color->id }}" class="update-status"
                                                    data-id="{{ $color->id }}" switch="success" data-on="Active"
                                                    data-off="Inactive" {{ $color->status === 'Active' ? 'checked' : '' }}
                                                    data-endpoint="{{ route('color-status') }}" />
                                                <label class="mb-0" for="{{ $color->id }}" data-on-label="Active"
                                                    data-off-label="Inactive"></label>
                                            </td>
                                            @canany(['edit colors', 'delete colors'])
                                                <td>
                                                    @if (Auth::user()->can('edit colors'))
                                                        <a href="{{ route('colors.edit', $color->id) }}"
                                                            class="btn btn-primary btn-sm edit py-0 px-1"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                    @endif
                                                    @if (Auth::user()->can('delete colors'))
                                                        <button data-source="Color"
                                                            data-endpoint="{{ route('colors.destroy', $color->id) }}"
                                                            class="delete-btn btn btn-danger btn-sm edit py-0 px-1">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            @endcanany
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
                    $('#datatable th, #datatable td').addClass('p-0');
                }
            });
        });
    </script>
@endsection
