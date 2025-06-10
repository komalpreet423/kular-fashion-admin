@extends('layouts.app')
@section('title', 'Order Details #'.$order->unique_order_id)

@section('header-button')
    <a href="{{ route('orders.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Go Back</a>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-2">Order #{{ $order->unique_order_id }}</h4>
                    <div class="row mb-2 border-bottom">
                        <div class="col-md-4">
                            <h5 class=" text-muted mb-1">Customer Info</h5>
                            <p class="mb-0"><strong>Name:</strong> {{ $order->user->name ?? 'N/A' }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $order->user->email ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <h5 class="text-muted mb-1">Order Details</h5>
                            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="d-flex align-items-center mb-1">
                                    <label for="Statusdropdown"
                                        class="form-label me-2 mb-0"><strong>Status:</strong></label>
                                    <select name="status" id="Statusdropdown" class="form-select form-select-sm">
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>
                                            Processing</option>
                                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped
                                        </option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>
                                            Delivered</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>
                                            Cancelled</option>
                                        <option value="returned" {{ $order->status === 'returned' ? 'selected' : '' }}>
                                            Returned</option>
                                    </select>
                                </div>


                                <p class="mb-0"><strong>Placed At:</strong>
                                    {{ $order->placed_at ? \Carbon\Carbon::parse($order->placed_at)->format('d-m-Y') : 'N/A' }}
                                </p>
                        </div>
                        <div class="col-md-4">
                            <h5 class=" text-muted mb-1">Payment Info</h5>
                            <p class="mb-1"><strong>Payment Type:</strong>
                                {{ ucfirst(str_replace('_', ' ', $order->payment_type)) }}</p>
                            <p class="mb-1"><strong>Payment Status:</strong> {{ ucfirst($order->payment_status) }}</p>
                            <p class="mb-2"><strong>Total Paid:</strong> ₹{{ number_format($order->total, 2) }}</p>
                        </div>

                        <div class="row mb-2">
                            <div class="col text-start">
                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="mb-2">
                        <h5 class="text-muted mb-2">Ordered Items</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th>Qty</th>
                                        <th>Rate</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->orderItems as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                                            <td>{{ $item->variant->name ?? '-' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>₹{{ number_format($item->offered_rate, 2) }}</td>
                                            <td>₹{{ number_format($item->price, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No items found in this order.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                        <td>₹{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Discount:</strong></td>
                                        <td>- ₹{{ number_format($order->discount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Tax:</strong></td>
                                        <td>₹{{ number_format($order->tax, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Shipping:</strong></td>
                                        <td>₹{{ number_format($order->shipping_charge, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                        <td>₹{{ number_format($order->total, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @if ($order->notes)
                        <div class="mt-3">
                            <h6 class="text-muted">Order Notes</h6>
                            <p>{{ $order->notes }}</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
    <x-include-plugins :plugins="['select2']" />
    <script>
        $(document).ready(function() {
            $('#Statusdropdown').select2({
                dropdownAutoWidth: true,
                width: '200px'
            });
        });
    </script>
    <style>
        .select2-container--default .select2-selection--single {
            height: 28px !important;
            padding-top: 2px !important;
        }

        .select2-container--default .select2-results__option {
            padding: 2px 6px !important;
            font-size: 12px !important;
            line-height: 1.3 !important;
        }

        span#select2-Statusdropdown-container {
            line-height: 23px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            top: 40% !important;
        }
    </style>
@endsection
