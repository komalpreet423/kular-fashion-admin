@extends('layouts.app')

@section('title', 'Pick List')

@section('header-button')
<div class="d-inline-block me-2">
</div>
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
                        <form id="filterForm" method="GET">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="branch">Branch</label>
                                    <select id="branch" name="branch" class="form-control">
                                        @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" @selected(request('branch', auth()->user()->branch_id) == $branch->id)>
                                            {{ $branch->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="date">Select Date</label>
                                    <input type="text" id="date" name="date" class="form-control" autocomplete="off" value="{{ request('date') ?? now()->format('Y-m-d') }}">
                                </div>

                                <div class="col-md-4 mt-4">
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-filter"></i> Apply Filters</button>
                                    <a href="{{ route('pick-list.index') }}" class="btn btn-secondary"><i class="bx bx-reset"></i> Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <table id="datatable-buttons" class="table table-bordered table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Branch</th>
                                    <th>Department</th>
                                    <th>Make</th>
                                    <th>Product Type</th>
                                    <th>Price</th>
                                    <th>Sold Price</th>
                                    <th>Description</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orderItems as $key => $item)
                                <tr>
                                    <td>{{ ++$key }}</td>
                                    <td>{{ $item->branch->name ?? 'N/A' }}</td>
                                    <td>{{ $item->product->department->name ?? 'N/A' }}</td>
                                    <td>{{ $item->brand_name ?? 'N/A' }}</td>
                                    <td>{{ $item->product->productType->name ?? 'N/A' }}</td>
                                    <td>{{ $item->original_price }}</td>
                                    <td>{{ $item->changed_price }}</td>
                                    <td>{{ $item->description ?? 'N/A' }}</td>
                                    <td>{{ $item->color_name ?? 'N/A' }}</td>
                                    <td>{{ $item->size ?? 'N/A' }}</td>
                                    <td>{{ $item->created_at->format('Y-m-d') }}</td>
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

<x-include-plugins :plugins="['dataTable', 'flatpickr']"></x-include-plugins>

<script>
    $(document).ready(function() {
        if (!$.fn.DataTable) {
            console.error("DataTables is not loaded!");
            return;
        }

        $("#datatable-buttons").DataTable({
            lengthChange: false
            , buttons: ["copy", "excel", "pdf", "colvis"]
            , order: [], 
            columnDefs: [{
                    targets: "_all"
                    , orderable: true
                } 
            ]
        }).buttons().container().appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");

        $(".dataTables_length select").addClass("form-select form-select-sm");

        var selectedDate = "{{ request('date') }}" || new Date().toISOString().slice(0, 16).replace("T", " ");

        flatpickr("#date", {
            enableTime: true
            , dateFormat: "Y-m-d "
            , defaultDate: selectedDate
            , allowInput: true
        });
    });

</script>

@endsection
