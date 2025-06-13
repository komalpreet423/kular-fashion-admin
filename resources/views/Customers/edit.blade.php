@extends('layouts.app')

@section('title', 'Edit Customer')
@section('header-button')
    <a href="{{ route('customers.index') }}" class="btn btn-primary"><i class="bx bx-arrow-back"></i> Go Back</a>
@endsection
@section('content')
    <div class="page-content">
        <div class="container-fluid">

            <x-error-message :message="$errors->first('message')" />
            <x-success-message :message="session('success')" />
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('customers.update', $customer->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name<span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name', $customer->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email<span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $customer->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control"
                                        value="{{ old('phone_number', $customer->phone_number) }}">
                                    @error('phone_number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="customersstatus" class="form-label">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="customersstatus" class="form-select" required>
                                        <option value="active"
                                            {{ old('status', $customer->status) === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive"
                                            {{ old('status', $customer->status) === 'inactive' ? 'selected' : '' }}>Inactive
                                        </option>
                                        <option value="suspended"
                                            {{ old('status', $customer->status) === 'suspended' ? 'selected' : '' }}>
                                            Suspended</option>
                                        <option value="pending"
                                            {{ old('status', $customer->status) === 'pending' ? 'selected' : '' }}>Pending
                                        </option>
                                    </select>
                                    @error('status')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Update Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <x-include-plugins :plugins="['select2']" />
    <script>
        $(document).ready(function() {
            $('#customersstatus').select2();
        });
    </script>
@endsection
