@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">
                    Coupons
                </h4>

                <p class="text-muted mb-0">
                    Manage store discounts and promotional codes.
                </p>
            </div>

            <a href="{{ route('admin.coupons.create') }}" class="btn btn-dark">
                Add Coupon
            </a>

        </div>


        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        <div class="card">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Discount</th>
                                <th>Scope</th>
                                <th>Usage</th>
                                <th>Expiry</th>
                                <th>Status</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($coupons as $coupon)
                                <tr>

                                    <td>
                                        <strong>
                                            {{ $coupon->code }}
                                        </strong>

                                        <div class="small text-muted">
                                            {{ $coupon->name }}
                                        </div>
                                    </td>


                                    <td>

                                        @if ($coupon->discount_type === 'percentage')
                                            {{ number_format($coupon->discount_value, 0) }}%
                                        @else
                                            ₦{{ number_format($coupon->discount_value, 2) }}
                                        @endif

                                    </td>


                                    <td>

                                        @if ($coupon->scope === 'general')
                                            <span class="badge bg-primary">
                                                General
                                            </span>
                                        @else
                                            <span class="badge bg-info">
                                                Product
                                            </span>

                                            <div class="small mt-1">

                                                {{ $coupon->product->name ??
                                                    ($coupon->product->product_name ?? ($coupon->product->title ?? 'Product #' . $coupon->product_id)) }}

                                            </div>
                                        @endif

                                    </td>


                                    <td>
                                        {{ $coupon->usage_count }}

                                        /

                                        {{ $coupon->usage_limit ?? '∞' }}
                                    </td>


                                    <td>

                                        @if ($coupon->expires_at)
                                            {{ $coupon->expires_at->format('d M Y') }}
                                        @else
                                            No expiry
                                        @endif

                                    </td>


                                    <td>

                                        <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="btn btn-sm
                                            {{ $coupon->is_active ? 'btn-success' : 'btn-secondary' }}">

                                                {{ $coupon->is_active ? 'Active' : 'Inactive' }}

                                            </button>

                                        </form>

                                    </td>


                                    <td>

                                        <div class="d-flex gap-2">

                                            <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                                class="btn btn-sm btn-outline-dark">
                                                Edit
                                            </a>


                                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                                onsubmit="
                                            return confirm(
                                                'Delete this coupon?'
                                            );
                                        ">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5 text-muted">
                                        No coupons have been created.
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>


                <div class="mt-3">
                    {{ $coupons->links() }}
                </div>

            </div>

        </div>

    </div>
@endsection
