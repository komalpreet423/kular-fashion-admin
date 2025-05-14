@extends('layouts.app')

@section('title', 'Weekely Turnover')

@section('header-button')
<div class="d-inline-block me-2">
</div>
@endsection

@push('styles')
<style>
    /* Minimalist DataTables pagination */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0 !important;
        margin: 0 !important;
        background: none !important;
        border: none !important;
        color: #000 !important;
        font-weight: normal;
        box-shadow: none !important;
        font-size: 14px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
    .dataTables_wrapper .dataTables_paginate .paginate_button.current,
    .dataTables_wrapper .dataTables_paginate .paginate_button:active {
        background: none !important;
        border: none !important;
        color: #000 !important;
        font-weight: normal;
        box-shadow: none !important;
    }

    .dataTables_wrapper .dataTables_paginate {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        align-items: center;
    }
    /* Center align all table headers and cells */
    #weekely-turnover-report-table thead th,
    #weekely-turnover-report-table tbody td,
    #weekely-turnover-report-table tfoot td {
        text-align: center;
        vertical-align: middle;
    }
</style>
@endpush


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
                            @include('best-brands-per-product-type.form')
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center flex-grow-1">
                                <!-- Placeholder for DataTable length and search -->
                            </div>
                            <div id="customExportButtons" class="btn-group" role="group" aria-label="Export Buttons">
                                <button type="button" class="btn btn-secondary" id="exportCopy">Copy</button>
                                <button type="button" class="btn btn-success" id="exportExcel">Excel</button>
                                <button type="button" class="btn btn-danger" id="exportPdf">PDF</button>
                            </div>
                        </div>
                        <table id="weekely-turnover-report-table" data-selected-weekely-turnover-reports="" data-unselected-weekely-turnover-reports=""
                            class="table table-bordered dt-responsive nowrap w-100 table-striped">
                            <thead>
                                <tr>
                                    <th>Branch Name <i class="fas fa-sort"></i></th>
                                    <th>Sales Unit <i class="fas fa-sort"></i></th>
                                    <th>Sales Value <i class="fas fa-sort"></i></th>
                                </tr>
                            </thead>


                            <tbody>
                                @if(!empty($filteredData) && count($filteredData) > 0)
                                    @foreach($filteredData as $key => $item)
                                    <tr>
                                        <td>{{ $item['branch_name'] ?? 'N/A' }}</td>
                                        <td>{{ $item['total_quantity'] ?? 'N/A' }}</td>
                                        <td>{{ $item['sales_value'] ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">No Data Available</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<x-include-plugins :plugins="['dataTable', 'flatpickr', 'select2']"></x-include-plugins>

@endsection

@push('scripts')

<script>
    let dataTable;

    $(document).ready(function () {
        dataTable = $('#weekely-turnover-report-table').DataTable({
            ordering: true,
            order: [],
            pageLength: 10,
            columns: [                
                { title: "Branch Name", data: "branch_name" },
                { title: "Sales Unit", data: "total_quantity" },
                { title: "Sales Value", data: "sales_value" }
            ],
            columnDefs: [
                {
                    targets: 2, // "Rank" column index
                    orderable: false
                }
            ],
            lengthMenu: [10, 25, 50, 100],
            language: {
                emptyTable: "No Data Available"
            },
            dom: '<"row mb-2"<"col-sm-6"l><"col-sm-6"f>>' + 
                 '<"row"<"col-12"tr>>' + 
                 '<"row mt-2"<"col-sm-6"i><"col-sm-6"p>>',
            buttons: [
                {
                    extend: 'copyHtml5',
                    title: 'Weekely Turnover',
                    exportOptions: {
                        columns: ':visible'
                    },
                    className: 'd-none',
                },
                {
                    extend: 'excelHtml5',
                    title: 'Weekely Turnover',
                    exportOptions: {
                        columns: ':visible'
                    },
                    className: 'd-none',
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Weekely Turnover',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (doc) {
                        doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) { return 0.5; };
                        objLayout['vLineWidth'] = function(i) { return 0.5; };
                        objLayout['hLineColor'] = function(i) { return '#aaa'; };
                        objLayout['vLineColor'] = function(i) { return '#aaa'; };
                        objLayout['paddingLeft'] = function(i) { return 8; };
                        objLayout['paddingRight'] = function(i) { return 8; };
                        objLayout['paddingTop'] = function(i) { return 6; };
                        objLayout['paddingBottom'] = function(i) { return 6; };
                        doc.content[1].layout = objLayout;
                    },
                    className: 'd-none'
                }
            ]
        });

        // Trigger export buttons
        $('#exportCopy').on('click', function () {
            dataTable.button('.buttons-copy').trigger();
        });

        $('#exportExcel').on('click', function () {
            dataTable.button('.buttons-excel').trigger();
        });

        $('#exportPdf').on('click', function () {
            dataTable.button('.buttons-pdf').trigger();
        });
    });
</script>

<script>
    $('#applyFilterBtn').on('click', function () {
        const formData = $('#filterForm').serialize();
        $.ajax({
            url: "{{ route('weekely-turnover.filter') }}",
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function () {
                $('#applyFilterBtn').attr('disabled', true).text('Loading...');
            },
            complete: function () {
                $('#applyFilterBtn').attr('disabled', false).text('Apply Filter');
            },
            success: function (response) {
                const table = $('#weekely-turnover-report-table').DataTable();
                table.clear();

                response.data.forEach((item) => {
                    table.row.add(item);
                });

                table.draw();
            },
            error: function (xhr) {
                alert('Failed to load data.');
            }
        });
    });
</script>

<script>
    $(function() {
        $('.form-select').select2();

        flatpickr('.best-brand-date-picker', {
            dateFormat: "d-m-y"
            , allowInput: true
            , maxDate: new Date()
        });

        $('input[name="show_options"]').change(function() {
            const firstLinesCount = $('input[name="first_lines_count"]');
            if ($(this).val() === 'first') {
                firstLinesCount.prop('disabled', false);
            } else {
                firstLinesCount.prop('disabled', true);
            }
        });
    });
</script>

@endpush