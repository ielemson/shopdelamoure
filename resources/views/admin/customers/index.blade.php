@extends('layouts.admin')

@section('content')
<!-- Body: Customers List -->
<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <h3 class="fw-bold mb-0">Customers</h3>

                    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary py-2 px-4">
                        <i class="icofont-plus me-2"></i>Add Customer
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-header py-3 bg-transparent border-bottom-0 d-flex justify-content-between align-items-center flex-wrap">
                        <h6 class="mb-0 fw-bold">Customer List</h6>

                        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex mt-2 mt-md-0">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="form-control me-2"
                                   placeholder="Search customer...">

                            <button type="submit" class="btn btn-secondary">
                                <i class="icofont-search"></i>
                            </button>
                        </form>
                    </div>

                    <div class="card-body">

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Registered</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($customers as $customer)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar rounded bg-primary text-white no-thumbnail me-2">
                                                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                                                    </div>

                                                    <div>
                                                        <strong>{{ $customer->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            Customer ID: #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>{{ $customer->email }}</td>

                                            <td>{{ $customer->phone ?? 'N/A' }}</td>

                                            <td>
                                                @php
                                                    $status = $customer->customerProfile->status ?? 'active';
                                                @endphp

                                                @if($status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($status === 'inactive')
                                                    <span class="badge bg-warning">Inactive</span>
                                                @else
                                                    <span class="badge bg-danger">Suspended</span>
                                                @endif
                                            </td>

                                            <td>
                                                {{ $customer->created_at->format('d M, Y') }}
                                            </td>

                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.customers.show', $customer->id) }}"
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="icofont-eye"></i>
                                                    </a>

                                                    <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="icofont-edit"></i>
                                                    </a>

                                                    <form action="{{ route('admin.customers.destroy', $customer->id) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Are you sure you want to delete this customer?')">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="icofont-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <h6 class="mb-1">No customers found</h6>
                                                <p class="text-muted mb-3">
                                                    Customers will appear here after registration or admin creation.
                                                </p>

                                                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                                                    <i class="icofont-plus me-2"></i>Add Customer
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($customers->hasPages())
                            <div class="mt-4">
                                {{ $customers->links() }}
                            </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection