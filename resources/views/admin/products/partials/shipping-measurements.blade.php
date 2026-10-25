<div class="card mb-3">
    <div class="card-header py-3">
        <h6 class="mb-0 fw-bold">Item Weight</h6>
    </div>
    <div class="card-body">
        <label for="shipping_weight_kg" class="form-label">Weight of one sellable unit (kg)</label>
        <input type="number" id="shipping_weight_kg" name="shipping_weight_kg"
            class="form-control @error('shipping_weight_kg') is-invalid @enderror"
            min="0.001" max="9999999.999" step="0.001"
            value="{{ old('shipping_weight_kg', data_get($shippingItem ?? null, 'shipping_weight_kg')) }}"
            aria-describedby="itemWeightHelp">
        @error('shipping_weight_kg')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <p id="itemWeightHelp" class="small text-muted mt-3 mb-0">
            Weigh one sellable unit with its bottle or jar and any retail packaging
            that always accompanies it. Exclude the outer dispatch box and extra
            shipping padding; these will be accounted for separately. Enter kg
            (divide a measured gram weight by 1,000). Do not convert ml directly to kg.
            Leave blank if unknown. Clearing this input removes the saved item weight.
        </p>
    </div>
</div>
