@php
    $editing = isset($coupon);

    $selectedScope = old('scope', $editing ? $coupon->scope : 'general');

    $selectedDiscountType = old('discount_type', $editing ? $coupon->discount_type : 'percentage');
@endphp

<div class="row">

    {{-- Name --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Coupon Name
        </label>

        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
            value="{{ old('name', $coupon->name ?? '') }}" placeholder="e.g. September Promotion" required>

        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- Code --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Coupon Code
        </label>

        <input type="text" name="code"
            class="form-control text-uppercase
                @error('code') is-invalid @enderror"
            value="{{ old('code', $coupon->code ?? '') }}" placeholder="e.g. SEPT10" required>

        @error('code')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- Discount Type --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Discount Type
        </label>

        <select name="discount_type" id="discount_type" class="form-select" required>
            <option value="percentage" {{ $selectedDiscountType === 'percentage' ? 'selected' : '' }}>
                Percentage (%)
            </option>

            <option value="fixed" {{ $selectedDiscountType === 'fixed' ? 'selected' : '' }}>
                Fixed Amount (₦)
            </option>
        </select>
    </div>


    {{-- Discount Value --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Discount Value
        </label>

        <input type="number" step="0.01" min="0" name="discount_value"
            class="form-control
                @error('discount_value') is-invalid @enderror"
            value="{{ old('discount_value', $coupon->discount_value ?? '') }}"
            required>

        @error('discount_value')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- Scope --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Coupon Applies To
        </label>

        <select name="scope" id="coupon_scope" class="form-select" required>
            <option value="general" {{ $selectedScope === 'general' ? 'selected' : '' }}>
                General — All Products
            </option>

            <option value="product" {{ $selectedScope === 'product' ? 'selected' : '' }}>
                Specific Product
            </option>
        </select>
    </div>


    {{-- Product --}}
    <div class="col-md-6 mb-4" id="productWrapper">
        <label class="form-label">
            Product
        </label>

        <select name="product_id" class="form-select
                @error('product_id') is-invalid @enderror">
            <option value="">
                Select Product
            </option>

            @foreach ($products as $product)
                @php
                    $productName =
                        $product->name ?? ($product->product_name ?? ($product->title ?? 'Product #' . $product->id));
                @endphp

                <option value="{{ $product->id }}"
                    {{ (string) old('product_id', $coupon->product_id ?? '') === (string) $product->id ? 'selected' : '' }}>
                    {{ $productName }}
                </option>
            @endforeach
        </select>

        @error('product_id')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>


    {{-- Minimum Order --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Minimum Order Amount (₦)
        </label>

        <input type="number" step="0.01" min="0" name="minimum_order_amount" class="form-control"
            value="{{ old('minimum_order_amount', $coupon->minimum_order_amount ?? 0) }}">
    </div>


    {{-- Maximum Discount --}}
    <div class="col-md-6 mb-4" id="maximumDiscountWrapper">
        <label class="form-label">
            Maximum Discount Amount (₦)
        </label>

        <input type="number" step="0.01" min="0" name="maximum_discount_amount" class="form-control"
            value="{{ old('maximum_discount_amount', $coupon->maximum_discount_amount ?? '') }}"
            placeholder="Optional">

        <small class="text-muted">
            Useful for percentage coupons.
        </small>
    </div>


    {{-- Usage Limit --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Total Usage Limit
        </label>

        <input type="number" min="1" name="usage_limit" class="form-control"
            value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}"
            placeholder="Leave blank for unlimited">
    </div>


    {{-- Per Customer --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Usage Per Customer
        </label>

        <input type="number" min="1" name="per_customer_limit" class="form-control"
            value="{{ old('per_customer_limit', $coupon->per_customer_limit ?? 1) }}">
    </div>


    {{-- Starts --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Starts At
        </label>

        <input type="datetime-local" name="starts_at" class="form-control"
            value="{{ old('starts_at', isset($coupon) && $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
    </div>


    {{-- Expires --}}
    <div class="col-md-6 mb-4">
        <label class="form-label">
            Expires At
        </label>

        <input type="datetime-local" name="expires_at" class="form-control"
            value="{{ old('expires_at', isset($coupon) && $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
    </div>


    {{-- Active --}}
    <div class="col-12 mb-4">
        <div class="form-check form-switch">

            <input type="hidden" name="is_active" value="0">

            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                {{ old('is_active', isset($coupon) ? $coupon->is_active : true) ? 'checked' : '' }}>

            <label class="form-check-label" for="is_active">
                Coupon is active
            </label>
        </div>
    </div>

</div>


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const scope = document.getElementById('coupon_scope');

            const productWrapper =
                document.getElementById('productWrapper');

            const discountType =
                document.getElementById('discount_type');

            const maximumDiscountWrapper =
                document.getElementById('maximumDiscountWrapper');


            function updateScope() {
                if (scope.value === 'product') {
                    productWrapper.style.display = '';
                } else {
                    productWrapper.style.display = 'none';
                }
            }


            function updateDiscountType() {
                if (discountType.value === 'percentage') {
                    maximumDiscountWrapper.style.display = '';
                } else {
                    maximumDiscountWrapper.style.display = 'none';
                }
            }


            scope.addEventListener(
                'change',
                updateScope
            );

            discountType.addEventListener(
                'change',
                updateDiscountType
            );


            updateScope();
            updateDiscountType();
        });
    </script>
@endpush
