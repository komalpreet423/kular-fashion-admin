@extends('layouts.app')
@section('title', 'Customer Orders')
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />
                    <div class="card">
                        <div class="card-body">
                            <form id="filterForm" method="GET" action="{{ route('orders.index') }}">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select name="user_id" id="UserFilter" class="form-select">
                                            <option value="">All Customers</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}"
                                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="payment_type" id="PaymentTypeFilter" class="form-select">
                                            <option value="">All Payment Types</option>
                                            <option value="cod"
                                                {{ request('payment_type') === 'cod' ? 'selected' : '' }}>Cash on Delivery
                                            </option>
                                            <option value="credit_debit_card"
                                                {{ request('payment_type') === 'credit_debit_card' ? 'selected' : '' }}>
                                                Credit/Debit Card</option>
                                            <option value="upi"
                                                {{ request('payment_type') === 'upi' ? 'selected' : '' }}>UPI</option>
                                            <option value="net_banking"
                                                {{ request('payment_type') === 'net_banking' ? 'selected' : '' }}>Net
                                                Banking</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" id="StatusFilter" class="form-select">
                                            <option value="">All Status</option>
                                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="processing"
                                                {{ request('status') === 'processing' ? 'selected' : '' }}>Processing
                                            </option>
                                            <option value="shipped"
                                                {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered"
                                                {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered
                                            </option>
                                            <option value="cancelled"
                                                {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled
                                            </option>
                                            <option value="returned"
                                                {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="payment_status" id="PaymentStatusFilter" class="form-select">
                                            <option value="">All Payment Status</option>
                                            <option value="pending"
                                                {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending
                                            </option>
                                            <option value="initiated"
                                                {{ request('payment_status') === 'initiated' ? 'selected' : '' }}>Initiated
                                            </option>
                                            <option value="authorized"
                                                {{ request('payment_status') === 'authorized' ? 'selected' : '' }}>
                                                Authorized</option>
                                            <option value="paid"
                                                {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                            <option value="failed"
                                                {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed
                                            </option>
                                            <option value="refunded"
                                                {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <table id="orders-datatable"
                                class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Payment Type</th>
                                        <th>Payment Status</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Placed At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders as $index => $order)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $order->unique_order_id }}</td>
                                            <td>{{ $order->user?->name ?? 'N/A' }}</td>
                                            <td>{{ ucfirst($order->payment_type) }}</td>
                                            <td>{{ ucfirst($order->payment_status) }}</td>
                                            <td>{{ number_format($order->total, 2) }}</td>
                                            <td>{{ ucfirst($order->status) }}
                                            </td>
                                            <td>{{ $order->placed_at ? \Carbon\Carbon::parse($order->placed_at)->format('d-m-Y') : 'N/A' }}
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
    <x-include-plugins :plugins="['dataTable', 'select2']" />
    <script>
        $(document).ready(function() {
            $('#StatusFilter,#UserFilter,#PaymentStatusFilter,#PaymentTypeFilter').select2();

            $('#StatusFilter,#UserFilter,#PaymentStatusFilter,#PaymentTypeFilter').on('change', function() {
                $('#filterForm').submit();
            });
            $('#orders-datatable').DataTable({
                order: [
                    [1, 'desc']
                ],
                drawCallback: function() {
                    $('#orders-datatable th, #orders-datatable td').addClass('p-1');
                }
            });
        });
    </script>
@endsection
