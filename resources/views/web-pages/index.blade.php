@extends('layouts.app')

@section('title', 'Web Pages')

@section('header-button')
    @can('create webpages')
        <a href="{{ route('webpages.create') }}" class="btn btn-primary">Add New Web Page</a>
    @endcan
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
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Hidden Categories</th>
                                        <th>All Filters</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($webPages as $key => $webPage)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ ucwords($webPage->title) }}</td>
                                            <td>{{ $webPage->slug }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $webPage->hide_categories ? 'danger' : 'success' }}">
                                                    {{ $webPage->hide_categories ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $webPage->show_all_filters ? 'success' : ($webPage->hide_all_filters ? 'danger' : 'secondary') }}">
                                                    {{ $webPage->show_all_filters ? 'Show All' : ($webPage->hide_all_filters ? 'Hide All' : 'Default') }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ config('app.frontend_url') }}/pages/webpages/{{ $webPage->slug }}"
                                                    class="btn btn-info btn-sm py-0 px-1" title="View page" target="_blank"
                                                    aria-label="View page">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('edit webpages')
                                                    <a href="{{ route('webpages.edit', $webPage->id) }}"
                                                        class="btn btn-primary btn-sm py-0 px-1" title="Edit">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                @endcan

                                                @can('delete webpages')
                                                    <button data-source="Web Page"
                                                        data-endpoint="{{ route('webpages.destroy', $webPage->id) }}"
                                                        class="delete-btn btn btn-danger btn-sm py-0 px-1" title="Delete">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                @endcan
                                            </td>
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

    <x-include-plugins :plugins="['dataTable', 'update-status']" />

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
