@extends('layouts.admin')

@section('content')
<!-- Body: Edit Customer -->
<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <h3 class="fw-bold mb-0">Edit Customer</h3>

                    <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary py-2 px-4">
                        <i class="icofont-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
            @csrf
            @method('PUT')

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
                                           value="{{ old('first_name', $customer->customerProfile->first_name ?? explode(' ', $customer->name)[0]) }}"
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
                                           value="{{ old('last_name', $customer->customerProfile->last_name ?? trim(str_replace(explode(' ', $customer->name)[0], '', $customer->name))) }}"
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
                                           value="{{ old('email', $customer->email) }}"
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
                                           value="{{ old('phone', $customer->phone) }}"
                                           class="form-control @error('phone') is-invalid @enderror">

                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Alternate Phone</label>

                                    <input type="text"
                                           name="alternate_phone"
                                           value="{{ old('alternate_phone', $customer->customerProfile->alternate_phone ?? '') }}"
                                           class="form-control @error('alternate_phone') is-invalid @enderror">

                                    @error('alternate_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Delivery Information</h6>
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Address</label>

                                <textarea name="address"
                                          rows="3"
                                          class="form-control @error('address') is-invalid @enderror"
                                          placeholder="Enter delivery address">{{ old('address', $customer->customerProfile->address ?? '') }}</textarea>

                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label class="form-label">City</label>

                                    <input type="text"
                                           name="city"
                                           value="{{ old('city', $customer->customerProfile->city ?? '') }}"
                                           class="form-control @error('city') is-invalid @enderror">

                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">State</label>

                                    <input type="text"
                                           name="state"
                                           value="{{ old('state', $customer->customerProfile->state ?? '') }}"
                                           class="form-control @error('state') is-invalid @enderror">

                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Country</label>

                                    <input type="text"
                                           name="country"
                                           value="{{ old('country', $customer->customerProfile->country ?? 'Nigeria') }}"
                                           class="form-control @error('country') is-invalid @enderror">

                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Postal Code</label>

                                    <input type="text"
                                           name="postal_code"
                                           value="{{ old('postal_code', $customer->customerProfile->postal_code ?? '') }}"
                                           class="form-control @error('postal_code') is-invalid @enderror">

                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="mt-3">
                                <label class="form-label">Delivery Note</label>

                                <textarea name="delivery_note"
                                          rows="3"
                                          class="form-control @error('delivery_note') is-invalid @enderror"
                                          placeholder="Any special delivery instruction">{{ old('delivery_note', $customer->customerProfile->delivery_note ?? '') }}</textarea>

                                @error('delivery_note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
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
                                        class="form-select @error('status') is-invalid @enderror">
                                    <option value="active"
                                        @selected(old('status', $customer->customerProfile->status ?? 'active') === 'active')>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        @selected(old('status', $customer->customerProfile->status ?? '') === 'inactive')>
                                        Inactive
                                    </option>

                                    <option value="suspended"
                                        @selected(old('status', $customer->customerProfile->status ?? '') === 'suspended')>
                                        Suspended
                                    </option>
                                </select>

                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Customer Type</label>

                                <select name="customer_type"
                                        class="form-select @error('customer_type') is-invalid @enderror">
                                    <option value="regular"
                                        @selected(old('customer_type', $customer->customerProfile->customer_type ?? 'regular') === 'regular')>
                                        Regular Customer
                                    </option>

                                    <option value="wholesale"
                                        @selected(old('customer_type', $customer->customerProfile->customer_type ?? '') === 'wholesale')>
                                        Wholesale Customer
                                    </option>

                                    <option value="vip"
                                        @selected(old('customer_type', $customer->customerProfile->customer_type ?? '') === 'vip')>
                                        VIP Customer
                                    </option>
                                </select>

                                @error('customer_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="icofont-save me-2"></i>Update Customer
                            </button>

                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Account Summary</h6>
                        </div>

                        <div class="card-body">
                            <p class="mb-2">
                                <strong>Customer ID:</strong>
                                #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}
                            </p>

                            <p class="mb-2">
                                <strong>Registered:</strong>
                                {{ $customer->created_at->format('d M, Y') }}
                            </p>

                            <p class="mb-0">
                                <strong>Last Updated:</strong>
                                {{ $customer->updated_at->format('d M, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                This page updates the customer’s account profile and delivery details.
                                Password changes should be handled separately for security.
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>
@endsection