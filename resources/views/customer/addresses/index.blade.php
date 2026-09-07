@extends('layouts.customer')

@section('CustomerContent')
    <div class="dashboard-page-content">

        <div class="row mb-9 align-items-center">
            <div class="col-sm-8">
                <h2 class="fs-4 mb-2">My Delivery Address</h2>
                <p class="mb-0 text-muted">You can save one delivery address. Update the form below whenever your delivery
                    details change.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card rounded-4 p-7 mb-7">
            <div class="card-header bg-transparent px-0 pt-0 pb-7 border-0">
                <h4 class="card-title fs-18px mb-1">{{ $address ? 'Update Address' : 'Add Address' }}</h4>
                <p class="text-muted fs-14px mb-0">Enter the address you want us to use for your deliveries.</p>
            </div>

            <div class="card-body px-0 py-0">
                <form action="{{ route('customer.addresses.store') }}" method="POST" class="form-border-1">
                    @csrf

                    <div class="row gx-9">
                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                value="{{ old('first_name', $address->first_name ?? '') }}" required>
                            @error('first_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">Last Name</label>
                            <input type="text" name="last_name" class="form-control"
                                value="{{ old('last_name', $address->last_name ?? '') }}" required>
                            @error('last_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">Phone</label>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone', $address->phone ?? ($user->phone ?? '')) }}" required>
                            @error('phone')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">City</label>
                            <input type="text" name="city" class="form-control"
                                value="{{ old('city', $address->city ?? '') }}" required>
                            @error('city')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">Country</label>
                            <select name="country_id" class="form-select" required>
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id', $address->country_id ?? '') == $country->id)>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">State</label>
                            <select name="state_id" class="form-select" required>
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" @selected(old('state_id', $address->state_id ?? '') == $state->id)>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('state_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">Street Address</label>
                            <input type="text" name="street_address" class="form-control"
                                value="{{ old('street_address', $address->street_address ?? '') }}" required>
                            @error('street_address')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control"
                                value="{{ old('postal_code', $address->postal_code ?? '') }}">
                            @error('postal_code')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-6">
                            <label class="mb-3 fs-13px ls-1 fw-semibold text-uppercase">Delivery Note</label>
                            <textarea name="delivery_note" class="form-control" rows="3">{{ old('delivery_note', $address->delivery_note ?? '') }}</textarea>
                            @error('delivery_note')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                {{ $address ? 'Update Address' : 'Save Address' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if ($address)
            <div class="card rounded-4 p-7 mb-7">
                <div class="card-header bg-transparent px-0 pt-0 pb-6 border-0">
                    <h4 class="card-title fs-18px mb-1">Saved Address</h4>
                    <p class="text-muted fs-14px mb-0">Your current delivery address.</p>
                </div>

                <div class="card-body px-0 py-0">
                    <div class="d-flex">
                        <span
                            class="square d-flex align-items-center justify-content-center rounded-circle bg-body-tertiary text-primary me-5"
                            style="--square-size:48px">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>

                        <div>
                            <h6 class="mb-2">{{ $address->first_name }} {{ $address->last_name }}</h6>
                            <p class="mb-1 text-muted">{{ $address->phone }}</p>
                            <p class="mb-1">{{ $address->street_address }}, {{ $address->city }},
                                {{ optional($address->state)->name }}, {{ optional($address->country)->name }}</p>

                            @if ($address->postal_code)
                                <p class="mb-1 text-muted">Postal Code: {{ $address->postal_code }}</p>
                            @endif

                            @if ($address->delivery_note)
                                <p class="mb-0 text-muted">Note: {{ $address->delivery_note }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection
