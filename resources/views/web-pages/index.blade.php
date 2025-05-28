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
                                            <th>Page Title</th>
                                                <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($webPages as $key => $webPage)
                                            <tr>
                                                <td>{{ ++$key }}</td>
                                                <td>{{ ucwords($webPage->title) }}</td>

                                                <td>
                                                    @can('edit webpages')
                                                        <a href="{{ route('webpages.edit', $webPage->id) }}"
                                                           class="btn btn-primary btn-sm edit py-0 px-1">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </a>
                                                        @endcan
                                                        @can('delete webpages')
                                                        <button data-source="Web Page"
                                                                data-endpoint="{{ route('webpages.destroy', $webPage->id) }}"
                                                                class="delete-btn btn btn-danger btn-sm edit py-0 px-1">
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
