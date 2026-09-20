@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">
                    Edit Coupon
                </h4>

                <p class="text-muted mb-0">
                    {{ $coupon->code }}
                </p>
            </div>

            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                Back
            </a>

        </div>


        <div class="card">

            <div class="card-body">

                <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">

                    @csrf
                    @method('PUT')

                    @include('admin.coupons._form')


                    <div class="mt-3">

                        <button type="submit" class="btn btn-dark">
                            Update Coupon
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
