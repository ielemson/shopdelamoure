@extends('layouts.admin')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h4 class="mb-1">
                    Create Coupon
                </h4>

                <p class="text-muted mb-0">
                    Create a new discount coupon.
                </p>
            </div>

            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                Back
            </a>

        </div>


        <div class="card">

            <div class="card-body">

                <form action="{{ route('admin.coupons.store') }}" method="POST">

                    @csrf

                    @include('admin.coupons._form')


                    <div class="mt-3">

                        <button type="submit" class="btn btn-dark">
                            Create Coupon
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection
