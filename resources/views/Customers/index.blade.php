@extends('layouts.app')

@section('title', 'Customers')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <x-error-message :message="$errors->first('message')" />
                    <x-success-message :message="session('success')" />
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('customers.index') }}"
                                class="mb-3 row g-3 align-items-center">
                                <div class="col-md-3">
                                    <select name="user_id" id="CustomerFilter" class="form-select"
                                        onchange="this.form.submit()">
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
                                    <select name="status" id="StatusFilter" class="form-select"
                                        onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>
                                            Suspended</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                            Pending</option>
                                    </select>
                                </div>
                            </form>
                            <table id="customer-table"
                                class="table table-bordered table-striped dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone Number</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customers as $index => $customer)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>{{ $customer->phone_number }}</td>
                                            <td>{{ ucfirst($customer->status) }}</td>
                                            <td>
                                                <a href="{{ route('customers.edit', $customer->id) }}"
                                                    class="btn btn-primary btn-sm py-0 px-1">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button data-source="Contact Message"
                                                    data-endpoint="{{ route('customers.destroy', $customer->id) }}"
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

    <x-include-plugins :plugins="['dataTable', 'delete', 'select2']" />

    <script>
        $(document).ready(function() {
            $('#CustomerFilter,#StatusFilter').select2();
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
