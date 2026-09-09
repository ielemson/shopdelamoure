@extends('layouts.admin')

@section('content')

    <!-- Body: Edit Shipping Rate -->
    <div class="body d-flex py-3">

        <div class="container-xxl">


            <!-- VALIDATION ERRORS -->
            @if ($errors->any())
                <div class="alert alert-danger">

                    <strong>Form error:</strong>

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>
            @endif


            <!-- ERROR MESSAGE -->
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif


            <form action="{{ route('admin.shipping-rates.update', $shippingRate->id) }}" method="POST">

                @csrf
                @method('PUT')


                <!-- PAGE HEADER -->
                <div class="row align-items-center">

                    <div class="border-0 mb-4">

                        <div
                            class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">

                            <div>

                                <h3 class="fw-bold mb-0">
                                    Edit Shipping Rate
                                </h3>

                                <small class="text-muted">

                                    Update the delivery zone and shipping charge for
                                    {{ $shippingRate->state->name ?? 'this location' }}.

                                </small>

                            </div>


                            <div>

                                <a href="{{ route('admin.shipping-rates.index') }}" class="btn btn-secondary me-2">

                                    <i class="icofont-arrow-left me-1"></i>

                                    Back

                                </a>


                                <button type="submit" class="btn btn-primary">

                                    <i class="icofont-save me-2"></i>

                                    Update Shipping Rate

                                </button>

                            </div>

                        </div>

                    </div>

                </div>



                <div class="row g-3 mb-3">


                    <!-- LEFT -->
                    <div class="col-xl-8 col-lg-8">


                        <!-- SHIPPING DETAILS -->
                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">

                                <h6 class="mb-0 fw-bold">
                                    Shipping Information
                                </h6>

                            </div>


                            <div class="card-body">


                                <div class="row g-3">


                                    <!-- COUNTRY -->
                                    <div class="col-md-6">

                                        <label class="form-label">

                                            Country

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <select name="country_id" id="country_id"
                                            class="form-select @error('country_id') is-invalid @enderror" required>

                                            <option value="">
                                                Select Country
                                            </option>


                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id', $shippingRate->country_id) == $country->id ? 'selected' : '' }}>

                                                    {{ $country->name }}

                                                </option>
                                            @endforeach

                                        </select>


                                        @error('country_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>



                                    <!-- STATE -->
                                    <div class="col-md-6">

                                        <label class="form-label">

                                            State

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <select name="state_id" id="state_id"
                                            class="form-select @error('state_id') is-invalid @enderror"
                                            data-selected="{{ old('state_id', $shippingRate->state_id) }}"
                                            required>

                                            <option value="">
                                                Select State
                                            </option>


                                            @foreach ($states as $state)
                                                <option value="{{ $state->id }}"
                                                    {{ old('state_id', $shippingRate->state_id) == $state->id ? 'selected' : '' }}>

                                                    {{ $state->name }}

                                                </option>
                                            @endforeach

                                        </select>


                                        @error('state_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>



                                    <!-- DELIVERY ZONE -->
                                    <div class="col-md-6">

                                        <label class="form-label">

                                            Delivery Zone

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <input type="text" name="zone_name"
                                            class="form-control @error('zone_name') is-invalid @enderror"
                                            value="{{ old('zone_name', $shippingRate->zone_name) }}"
                                            placeholder="e.g. Maitama, Wuse, Garki" required>


                                        @error('zone_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>



                                    <!-- SHIPPING COST -->
                                    <div class="col-md-6">

                                        <label class="form-label">

                                            Shipping Cost

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <div class="input-group">

                                            <span class="input-group-text">
                                                ₦
                                            </span>


                                            <input type="number" name="shipping_cost" step="0.01" min="0"
                                                class="form-control @error('shipping_cost') is-invalid @enderror"
                                                value="{{ old('shipping_cost', $shippingRate->shipping_cost) }}"
                                                placeholder="0.00" required>

                                        </div>


                                        @error('shipping_cost')
                                            <div class="text-danger small mt-1">
                                                {{ $message }}
                                            </div>
                                        @enderror


                                        <small class="text-muted d-block mt-2">
                                            Base shipping rate is stored in NGN.
                                        </small>

                                    </div>



                                    <!-- COVERED AREAS -->
                                    <div class="col-md-12">

                                        <label class="form-label">

                                            Covered Areas / Locations

                                            <span class="text-danger">
                                                *
                                            </span>

                                        </label>


                                        <textarea name="areas" rows="6" class="form-control @error('areas') is-invalid @enderror"
                                            placeholder="Enter locations separated by commas or new lines" required>{{ old('areas', $shippingRate->areas->pluck('name')->implode("\n")) }}</textarea>


                                        @error('areas')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror


                                        <small class="text-muted d-block mt-2">

                                            Enter each location on a new line or separate
                                            locations with commas.

                                            Example: Ministers Hill, Maitama Extension, Mpape

                                        </small>

                                    </div>


                                </div>


                            </div>

                        </div>


                    </div>



                    <!-- RIGHT -->
                    <div class="col-xl-4 col-lg-4">


                        <!-- STATUS -->
                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">

                                <h6 class="mb-0 fw-bold">
                                    Shipping Settings
                                </h6>

                            </div>


                            <div class="card-body">


                                <!-- IMPORTANT FOR UNCHECKED VALUE -->
                                <input type="hidden" name="is_active" value="0">


                                <div class="form-check form-switch mb-3">

                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                        value="1"
                                        {{ old('is_active', $shippingRate->is_active) ? 'checked' : '' }}>


                                    <label class="form-check-label" for="is_active">

                                        Active Shipping Rate

                                    </label>

                                </div>


                                <small class="text-muted">

                                    When disabled, customers will not be able
                                    to select this delivery zone during checkout.

                                </small>


                            </div>

                        </div>



                        <!-- CURRENT CONFIGURATION -->
                        <div class="card mb-3">

                            <div class="card-header py-3 bg-transparent border-bottom-0">

                                <h6 class="mb-0 fw-bold">
                                    Current Configuration
                                </h6>

                            </div>


                            <div class="card-body">


                                <!-- COUNTRY -->
                                <div class="mb-3">

                                    <small class="text-muted d-block">
                                        Country
                                    </small>

                                    <strong>
                                        {{ $shippingRate->country->name ?? '-' }}
                                    </strong>

                                </div>



                                <!-- STATE -->
                                <div class="mb-3">

                                    <small class="text-muted d-block">
                                        State
                                    </small>

                                    <strong>
                                        {{ $shippingRate->state->name ?? '-' }}
                                    </strong>

                                </div>



                                <!-- DELIVERY ZONE -->
                                <div class="mb-3">

                                    <small class="text-muted d-block">
                                        Delivery Zone
                                    </small>

                                    <strong>
                                        {{ $shippingRate->zone_name ?? '-' }}
                                    </strong>

                                </div>



                                <!-- COVERED AREAS -->
                                <div class="mb-3">

                                    <small class="text-muted d-block mb-2">
                                        Covered Areas
                                    </small>


                                    @if ($shippingRate->areas && $shippingRate->areas->count())
                                        <div class="d-flex flex-wrap gap-1">

                                            @foreach ($shippingRate->areas as $area)
                                                <span class="badge bg-light text-dark border">

                                                    {{ $area->name }}

                                                </span>
                                            @endforeach

                                        </div>
                                    @else
                                        <span class="text-muted">
                                            No covered areas
                                        </span>
                                    @endif

                                </div>



                                <!-- SHIPPING COST -->
                                <div class="mb-3">

                                    <small class="text-muted d-block">
                                        Shipping Cost
                                    </small>

                                    <strong class="fs-5">

                                        ₦{{ number_format($shippingRate->shipping_cost, 2) }}

                                    </strong>

                                </div>



                                <!-- STATUS -->
                                <div>

                                    <small class="text-muted d-block mb-1">
                                        Status
                                    </small>


                                    @if ($shippingRate->is_active)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>
                                    @endif

                                </div>


                            </div>

                        </div>


                    </div>


                </div>


            </form>


        </div>

    </div>



    <!-- DYNAMIC STATE LOADING -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const countrySelect =
                document.getElementById('country_id');

            const stateSelect =
                document.getElementById('state_id');

            const selectedState =
                stateSelect.dataset.selected;



            function loadStates(
                countryId,
                selectedStateId = null
            ) {

                if (!countryId) {

                    stateSelect.innerHTML =
                        '<option value="">Select State</option>';

                    stateSelect.disabled = false;

                    return;

                }


                stateSelect.disabled = true;

                stateSelect.innerHTML =
                    '<option value="">Loading states...</option>';


                fetch(
                        `/admin/shipping-rates/states/${countryId}`
                    )

                    .then(response => {

                        if (!response.ok) {

                            throw new Error(
                                'Unable to load states'
                            );

                        }

                        return response.json();

                    })


                    .then(states => {

                        stateSelect.innerHTML =
                            '<option value="">Select State</option>';


                        states.forEach(state => {

                            const option =
                                document.createElement('option');


                            option.value =
                                state.id;


                            option.textContent =
                                state.name;


                            if (
                                selectedStateId &&
                                selectedStateId == state.id
                            ) {

                                option.selected = true;

                            }


                            stateSelect.appendChild(option);

                        });


                        stateSelect.disabled = false;

                    })


                    .catch(error => {

                        console.error(error);


                        stateSelect.innerHTML =
                            '<option value="">Unable to load states</option>';


                        stateSelect.disabled = false;

                    });

            }



            /*
            |--------------------------------------------------------------------------
            | Country Change
            |--------------------------------------------------------------------------
            */

            countrySelect.addEventListener(
                'change',
                function() {

                    loadStates(
                        this.value
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Restore State If Validation Returned The Form
            |--------------------------------------------------------------------------
            */

            if (
                countrySelect.value &&
                !stateSelect.options.length
            ) {

                loadStates(
                    countrySelect.value,
                    selectedState
                );

            }

        });
    </script>

@endsection
