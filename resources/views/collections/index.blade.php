@extends('layouts.app')

@section('title', 'Collections')
@section('header-button')
    @if (Auth::user()->can('create collections'))
        <a href="{{ route('collections.create') }}" class="btn btn-primary">Add New Collection</a>
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
                                        <th>Name</th>
                                        <th>Image</th>
                                        @canany(['edit collections', 'delete collections'])
                                            <th>Action</th>
                                        @endcanany
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($collections as $key => $collection)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ ucwords($collection->name) }}</td>
                                            <td>
                                                @if ($collection->image && file_exists(public_path($collection->image)))
                                                    <img src="{{ asset($collection->image) }}" alt="Collection Image"
                                                        width="40" height="40" class="rounded">
                                                @else
                                                    <span class="text-muted">No Image</span>
                                                @endif
                                            </td>
                                            @canany(['edit collections', 'delete collections'])
                                                <td>
                                                    @if (Auth::user()->can('edit collections'))
                                                        <a href="{{ route('collections.edit', $collection->id) }}"
                                                            class="btn btn-primary btn-sm edit py-0 px-1"><i
                                                                class="fas fa-pencil-alt"></i></a>
                                                    @endif
                                                    @if (Auth::user()->can('delete collections'))
                                                        <button data-source="Collection"
                                                            data-endpoint="{{ route('collections.destroy', $collection->id) }}"
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
