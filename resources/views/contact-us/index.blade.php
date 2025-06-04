@extends('layouts.app')

@section('title', 'Contact Us')

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
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Message</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($messages as $index => $message)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $message->name }}</td>
                                                <td>{{ $message->email }}</td>
                                                <td>{{ $message->message }}</td>
                                                <td>
                                                    <button data-source="Contact Message"
                                                        data-endpoint="{{ route('contact-us.destroy', $message->id) }}"
                                                        class="delete-btn btn btn-danger btn-sm edit py-0 px-1">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
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

    <x-include-plugins :plugins="['dataTable', 'delete']" />

    <script>
        $(document).ready(function() {
            $('#datatable').DataTable({
                order: [
                    [0, 'asc']
                ],
                drawCallback: function(settings) {
                    $('#datatable th, #datatable td').addClass('p-0');
                }
            });
        });
    </script>
@endsection
