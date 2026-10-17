@extends('layouts.admin')

@section('content')
    <div class="body d-flex py-3">
        <div class="container-xxl">

            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4 gap-3 flex-wrap">
                <div>
                    <h3 class="fw-bold mb-1">Customers</h3>
                    <p class="text-muted mb-0">
                        Manage customer accounts and contact details.
                    </p>
                </div>

                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary py-2 px-4">
                    <i class="icofont-plus me-2"></i>Add Customer
                </a>
            </div>

            <div class="card">
                <div class="card-header py-3 bg-transparent border-bottom-0">
                    <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                        <h6 class="mb-0 fw-bold">Customer List</h6>

                        <form action="{{ route('admin.customers.index') }}" method="GET" class="d-flex gap-2">

                            <input type="search" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Search customers..." aria-label="Search customers">

                            <button type="submit" class="btn btn-secondary" aria-label="Search">
                                <i class="icofont-search"></i>
                            </button>

                            @if (request()->filled('search'))
                                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($customers as $customer)
                                    @php
                                        $status = $customer->customerProfile->status ?? 'active';

                                        $statusClass = match ($status) {
                                            'active' => 'bg-success',
                                            'inactive' => 'bg-warning text-dark',
                                            'suspended' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp

                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div
                                                    class="avatar rounded bg-primary text-white no-thumbnail me-3 flex-shrink-0">
                                                    {{ mb_strtoupper(mb_substr($customer->name ?? '', 0, 1)) }}
                                                </div>

                                                <div>
                                                    <span class="fw-bold">
                                                        {{ $customer->name }}
                                                    </span>

                                                    <div class="small text-muted">
                                                        Customer ID:
                                                        #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>{{ $customer->email }}</td>

                                        <td class="text-nowrap">
                                            {{ $customer->phone ?: 'N/A' }}
                                        </td>

                                        <td>
                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($status) }}
                                            </span>
                                        </td>

                                        <td class="text-nowrap">
                                            {{ $customer->created_at?->format('d M, Y') ?? 'N/A' }}
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.customers.show', $customer->id) }}"
                                                    class="btn btn-outline-secondary btn-sm" title="View customer"
                                                    aria-label="View {{ $customer->name }}">
                                                    <i class="icofont-eye"></i>
                                                </a>

                                                <a href="{{ route('admin.customers.edit', $customer->id) }}"
                                                    class="btn btn-outline-primary btn-sm" title="Edit customer"
                                                    aria-label="Edit {{ $customer->name }}">
                                                    <i class="icofont-edit"></i>
                                                </a>

                                                <form action="{{ route('admin.customers.destroy', $customer->id) }}"
                                                    method="POST" class="js-delete-customer m-0"
                                                    data-customer-name="{{ $customer->name }}">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                                        title="Delete customer" aria-label="Delete {{ $customer->name }}">
                                                        <i class="icofont-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="icofont-users-alt-3 fs-1 text-muted"></i>

                                            <h6 class="mt-3 mb-2">No customers found</h6>

                                            <p class="text-muted mb-3">
                                                @if (request()->filled('search'))
                                                    Try another name, email address or phone number.
                                                @else
                                                    Customers will appear here after registration
                                                    or admin creation.
                                                @endif
                                            </p>

                                            @if (request()->filled('search'))
                                                <a href="{{ route('admin.customers.index') }}"
                                                    class="btn btn-outline-secondary">
                                                    Clear Search
                                                </a>
                                            @else
                                                <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
                                                    <i class="icofont-plus me-2"></i>Add Customer
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($customers->hasPages())
                        <div class="mt-4">
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const initializeCustomerAlerts = () => {
                const successMessage = @json(session('success'));
                const errorMessage = @json(session('error'));

                if (successMessage || errorMessage) {
                    Swal.fire({
                        icon: successMessage ? 'success' : 'error',
                        title: successMessage ? 'Success' : 'Unable to complete action',
                        text: successMessage || errorMessage,
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-primary px-4'
                        }
                    });
                }

                document.querySelectorAll('.js-delete-customer').forEach((form) => {
                    let confirming = false;
                    let submitting = false;

                    form.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        if (confirming || submitting) {
                            return;
                        }

                        confirming = true;

                        try {
                            const customerName =
                                form.dataset.customerName || 'this customer';

                            const result = await Swal.fire({
                                icon: 'warning',
                                title: 'Delete customer?',
                                text: `Are you sure you want to delete ${customerName}?`,
                                showCancelButton: true,
                                confirmButtonText: 'Yes, delete customer',
                                cancelButtonText: 'Cancel',
                                focusCancel: true,
                                reverseButtons: true,
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-danger mx-1',
                                    cancelButton: 'btn btn-outline-secondary mx-1'
                                }
                            });

                            if (!result.isConfirmed) {
                                return;
                            }

                            submitting = true;

                            const button = form.querySelector('button[type="submit"]');

                            button.disabled = true;
                            button.setAttribute('aria-label', 'Deleting customer');
                            button.innerHTML =
                                '<span class="spinner-border spinner-border-sm" aria-hidden="true"></span>';

                            HTMLFormElement.prototype.submit.call(form);
                        } finally {
                            confirming = false;
                        }
                    });
                });
            };

            if (document.readyState === 'loading') {
                document.addEventListener(
                    'DOMContentLoaded',
                    initializeCustomerAlerts, {
                        once: true
                    }
                );
            } else {
                initializeCustomerAlerts();
            }
        })();
    </script>
    @push('scripts')
        {{-- Load once only; remove this if SweetAlert2 is already in your admin bundle. --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    @endpush
@endsection
