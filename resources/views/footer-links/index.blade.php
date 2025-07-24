@extends('layouts.app')
@section('title', 'Blocks Links')

@section('header-button')
    <a href="{{ route('footer-links.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New block 
    </a>
@endsection

@section('content')
<div class="page-content">
    <div class="container-fluid">
        <x-error-message :message="$errors->first('message')" />
        <x-success-message :message="session('success')" />

        <div class="card">
            <div class="card-body">
                <table id="customer-table" class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Key</th>
                            <th>Description</th>
                            <th>Attributes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blocks as $index => $block)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $block->name }}</td>
                                <td>{{ $block->key }}</td>
                                <td>{{ $block->description }}</td>
                                <td>
                                    @foreach($block->attributes as $attr)
                                        <div>
                                            <strong>{{ $attr->name }}</strong>
                                              <strong>{{ $attr->text }}</strong>
                                           
                                            @if($attr->image_path)
                                                <div>
                                                    <img src="{{ asset('storage/' . $attr->image_path) }}" style="max-height: 50px;">
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('footer-links.edit', $block) }}" class="btn btn-sm btn-primary py-0 px-1"><i class="fas fa-edit"></i></a>
                                    <button data-endpoint="{{ route('footer-links.destroy', $block) }}"
                                                    class="delete-btn btn btn-danger btn-sm  py-0 px-1"
                                                    >
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
 <x-include-plugins :plugins="['dataTable', 'delete', 'select2']" />

    <script>
        $(document).ready(function() {
            $('#customer-table').DataTable({
                order: [
                    [0, 'asc']
                ],
                drawCallback: function(settings) {
                    $('#customer-table th, #customer-table td').addClass('p-0');
                }
            });
        });
    </script>
@endsection