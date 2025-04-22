@extends('layouts.app', ['isVueComponent' => true])

@section('title', 'Inventory Transfer History')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />

                    <div class="card">
                        <div class="card-body">
                            <table id="datatable"
                                class="table table-sm table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Sent From</th>
                                        <th>Sent To</th>
                                        <th>Sent By</th>
                                        <th>Transfer Items</th>
                                        <th>Transfer Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventory_transfer as $key => $transfer)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $transfer->sentFrom->name }}</td>
                                            <td>{{ $transfer->sentTo->name }}</td>
                                            <td>{{ $transfer->sentBy->name }}</td>
                                            <td>{{ count($transfer->inventoryItems) }}</td>
                                            <td>{{ date('m-d-Y h:i:s', strtotime($transfer->created_at)) }}</td>
                                            <td>
                                                <a href="{{ route('inventory-transfer-view', ['id' => $transfer->id]) }}"
                                                    class="btn btn-secondary btn-sm  py-0 px-1">
                                                    <i class="fas fa-eye"></i>
                                                </a>
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
                    targets: 0
                }],
                order: [
                    [5, 'desc']
                ],
                drawCallback: function(settings) {
                    $('#datatable th, #datatable td').addClass('p-1');
                }
            });

        });
    </script>
@endsection
