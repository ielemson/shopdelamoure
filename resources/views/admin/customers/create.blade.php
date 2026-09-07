@extends('layouts.admin')

@section('content')

<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <h3 class="fw-bold mb-0">Add Customer</h3>

                    <a href="{{ route('admin.customers.index') }}"
                       class="btn btn-secondary py-2 px-4">
                        <i class="icofont-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.customers.store') }}" method="POST">
            @csrf

            <div class="row g-3">

                <!-- Customer Information -->
                <div class="col-xl-8 col-lg-8">

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Customer Information</h6>
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        First Name <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="first_name"
                                           value="{{ old('first_name') }}"
                                           class="form-control @error('first_name') is-invalid @enderror">

                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Last Name <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="last_name"
                                           value="{{ old('last_name') }}"
                                           class="form-control @error('last_name') is-invalid @enderror">

                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Email Address <span class="text-danger">*</span>
                                    </label>

                                    <input type="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           class="form-control @error('email') is-invalid @enderror">

                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           class="form-control @error('phone') is-invalid @enderror">

                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Password -->
                    <div class="card">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Login Credentials</h6>
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Password <span class="text-danger">*</span>
                                    </label>

                                    <input type="password"
                                           name="password"
                                           class="form-control @error('password') is-invalid @enderror">

                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Confirm Password <span class="text-danger">*</span>
                                    </label>

                                    <input type="password"
                                           name="password_confirmation"
                                           class="form-control">
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                <!-- Settings -->
                <div class="col-xl-4 col-lg-4">

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Customer Settings</h6>
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Customer Status</label>

                                <select name="status"
                                        class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>

                            <button type="submit"
                                    class="btn btn-primary w-100 py-2">
                                <i class="icofont-save me-2"></i>
                                Create Customer
                            </button>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">

                            <div class="alert alert-info mb-0">
                                Customers can update their delivery address,
                                profile information and shipping details
                                from their account dashboard after login.
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>
@endsection