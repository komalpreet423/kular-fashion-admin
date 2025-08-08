@extends('layouts.app')

@section('title', 'Size Scales')
@section('header-button')
    @if (Auth::user()->can('create size scales'))
        <a href="{{ route('size-scales.create') }}" class="btn btn-primary">Add New Size Scale</a>
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
                            <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Size Scale</th>
                                        <th>Total Sizes</th>
                                        <th>Products</th>
                                        <th>Status</th>
                                        @canany(['view size', 'edit size scales', 'delete size scales'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sizeScales as $key => $sizeScale)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ ucwords($sizeScale->name) }}
                                                @if (count($sizeScale->sizes) > 0)
                                                    ({{ $sizeScale->sizes->first()->size }} -
                                                    {{ $sizeScale->sizes->last()->size }})
                                                @endif

                                                @if($sizeScale->is_default)
                                                    <span class="badge rounded-pill bg-success">Default</span>
                                                @endif
                                            </td>
                                            <td>{{ $sizeScale->sizes_count }}</td>
                                            <td><a href="{{ route('index.related-products',['sizeScale',$sizeScale->id]) }}">{{ $sizeScale->products_count }}</a></td>
                                            <td>
                                                <input type="checkbox" id="{{ $sizeScale->id }}" class="update-status"
                                                    data-id="{{ $sizeScale->id }}" switch="success" data-on="Active"
                                                    data-off="Inactive"
                                                    {{ $sizeScale->status === 'Active' ? 'checked' : '' }}
                                                    data-endpoint="{{ route('size-scale-status') }}" />
                                                <label class="mb-0" for="{{ $sizeScale->id }}" data-on-label="Active"
                                                    data-off-label="Inactive"></label>
                                            </td>
                                            @canany(['view size', 'edit size scales', 'delete size scales'])
                                                <td>
                                                    @if (Auth::user()->can('view size'))
                                                        <a href="{{ route('sizes.index', $sizeScale->id) }}"
                                                            class="btn btn-primary btn-sm py-0 px-1">Manage Size</a>
                                                    @endif
                                                    @if (Auth::user()->can('create size scales'))
                                                        <a href="{{ route('size-scales.edit', $sizeScale->id) }}"
                                                            class="btn btn-primary btn-sm edit py-0 px-1"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                    @endif
                                                    @if (Auth::user()->can('delete size scales'))
                                                        <button data-source="Size Scale"
                                                            data-endpoint="{{ route('size-scales.destroy', $sizeScale->id) }}"
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
