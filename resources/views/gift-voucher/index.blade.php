@extends('layouts.app')
@section('title', 'Gift Vouchers')
@section('header-button')
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
                            <form id="filterForm" method="GET" action="{{ route('gift-voucher.index') }}">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select name="user_id" id="UserFilter"class="form-select">
                                            <option value="">All Users</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="payment_method" id="paymentmethod" class="form-select">
                                            <option value="">All Payment Methods</option>
                                            <option value="credit_card"
                                                {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>
                                                Credit Card
                                            </option>
                                            <option value="upi"
                                                {{ request('payment_method') == 'upi' ? 'selected' : '' }}>
                                                UPI
                                            </option>
                                            <option value="net_banking"
                                                {{ request('payment_method') == 'net_banking' ? 'selected' : '' }}>
                                                Net Banking
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select name="status" id="giftvoucherstatus" class="form-select">
                                            <option value="">All Status</option>
                                            @foreach ($statusOptions as $status)
                                                <option value="{{ $status }}"
                                                    {{ request('status') == $status ? 'selected' : '' }}>
                                                    {{ ucfirst($status) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <table id="datatable" class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>User</th>
                                        <th>Recipient Email</th>
                                        <th>Sender Name</th>
                                        <th>Delivery Date</th>
                                        <th>Amount</th>
                                        <th>Card Number</th>
                                        <th>Payment Method</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($giftVouchers as $key => $voucher)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $voucher->user->name ?? 'Guest' }}</td>
                                            <td>{{ $voucher->recipient_email }}</td>
                                            <td>{{ $voucher->sender_name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($voucher->delivery_date)->format('d-m-Y') }}</td>
                                            <td>${{ number_format($voucher->amount, 2) }}</td>
                                            <td>{{ $voucher->card_number }}</td>
                                            <td>{{ ucfirst($voucher->payment_method) }}</td>
                                            <td>{{ ucfirst($voucher->status) }}</td>
                                            <td class="d-flex">
                                                <a href="{{ route('gift-voucher.edit', $voucher->id) }}"
                                                    class="btn btn-primary btn-sm  py-0 px-1 mx-1">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button data-endpoint="{{ route('gift-voucher.destroy', $voucher->id) }}"
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
        </div>
    </div>
    <x-include-plugins :plugins="['dataTable', 'update-status', 'select2']"></x-include-plugins>
    <script>
        $(document).ready(function() {
            $('#UserFilter,#giftvoucherstatus,#paymentmethod').select2();
            $('#datatable').DataTable({
                columnDefs: [{
                    type: 'string',
                    targets: 1
                }],
                order: [
                    [1, 'asc']
                ],
                drawCallback: function() {
                    $('#datatable th, #datatable td').addClass('p-0');
                }
            });
            $('#UserFilter, #giftvoucherstatus, #paymentmethod').on('change', function() {
                $('#filterForm').submit();
            });
        });
    </script>
@endsection
