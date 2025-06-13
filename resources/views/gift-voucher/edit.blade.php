@extends('layouts.app')

@section('title', 'Edit Gift Voucher')
@section('header-button')
    <a href="{{ route('gift-voucher.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Go Back</a>
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
                            <form action="{{ route('gift-voucher.update', $voucher->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row mb-3">
                                    <div class="form-group col-md-3">
                                        <label for="recipient_email">Recipient Email<span
                                                class="text-danger">*</span></label>
                                        <input type="email" id="recipient_email" name="recipient_email"
                                            class="form-control" value="{{ $voucher->recipient_email }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="sender_name">Sender Name<span class="text-danger">*</span></label>
                                        <input type="text" id="sender_name" name="sender_name" class="form-control"
                                            value="{{ $voucher->sender_name }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="delivery_date">Delivery Date<span class="text-danger">*</span></label>
                                        <input type="text" id="delivery_date" name="delivery_date" class="form-control"
                                            value="{{ \Carbon\Carbon::parse($voucher->delivery_date)->format('d-m-Y') }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="amount">Amount<span class="text-danger">*</span></label>
                                        <input type="number" id="amount" name="amount" step="0.01"
                                            class="form-control" value="{{ $voucher->amount }}">
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="form-group col-md-3">
                                        <label for="card_number">Card Number<span class="text-danger">*</span></label>
                                        <input type="text" id="card_number" name="card_number" class="form-control"
                                            value="{{ $voucher->card_number }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="transaction_id">Transaction ID<span class="text-danger">*</span></label>
                                        <input type="text" id="transaction_id" name="transaction_id" class="form-control"
                                            value="{{ $voucher->transaction_id }}">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="payment_method">Payment Method <span
                                                class="text-danger">*</span></label>
                                        <select name="payment_method" id="payment_method" class="form-select">
                                            <option value="">Select Payment Method</option>
                                            
                                            <option value="credit_card"
                                                {{ $voucher->payment_method === 'credit_card' ? 'selected' : '' }}>
                                                Credit Card</option>
                                            <option value="upi"
                                                {{ $voucher->payment_method === 'upi' ? 'selected' : '' }}>UPI</option>
                                            <option value="net_banking"
                                                {{ $voucher->payment_method === 'net_banking' ? 'selected' : '' }}>Net
                                                Banking</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="giftvoucherstatus">Status<span class="text-danger">*</span></label>
                                        <select name="status" id="giftvoucherstatus" class="form-control">
                                            <option value="active" {{ $voucher->status == 'active' ? 'selected' : '' }}>
                                                Active</option>
                                            <option value="redeemed"
                                                {{ $voucher->status == 'redeemed' ? 'selected' : '' }}>
                                                Redeemed</option>
                                            <option value="expired" {{ $voucher->status == 'expired' ? 'selected' : '' }}>
                                                Expired</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">Update Voucher</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-include-plugins :plugins="['datePicker', 'select2']" />
    <script>
        $(document).ready(function() {
            $('#giftvoucherstatus,#payment_method').select2();
            $(function() {
                flatpickr('.date-picker', {
                    dateFormat: "d-m-Y",
                    allowInput: true,
                    maxDate: "today"
                });
                flatpickr('#delivery_date', {
                    dateFormat: "d-m-Y",
                    allowInput: true,
                    minDate: "today",
                    defaultDate: "{{ \Carbon\Carbon::parse($voucher->delivery_date)->format('d-m-Y') }}"
                });
            });
        });
    </script>
@endsection
