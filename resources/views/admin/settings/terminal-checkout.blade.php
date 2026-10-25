@extends('layouts.admin')
@section('content')
<div class="container-xxl py-4">
    <h3>Terminal Africa Checkout</h3>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif
    <div class="card mb-4"><div class="card-body">
        <h5>1. Preview checkout in sandbox</h5>
        <p>Add one product to your shopping cart, quantity 1. Save its actual item weight first. Select a box that fits it. Preview applies only to this browser session for 30 minutes. Other customers keep the active delivery provider.</p>
        <form method="POST" action="{{ route('admin.terminal-checkout.preview') }}">
            @csrf
            <label for="preview-package">Saved box</label>
            <select class="form-select mb-3" id="preview-package" name="package_id" required>
                <option value="">Select packaging</option>
                @foreach($packages as $package)<option value="{{ $package->id }}">{{ $package->name }}</option>@endforeach
            </select>
            <button class="btn btn-primary">Open sandbox checkout preview</button>
        </form>
        <form method="POST" action="{{ route('admin.terminal-checkout.stop') }}" class="mt-2">@csrf<button class="btn btn-outline-secondary">End preview</button></form>
    </div></div>
    <div class="card"><div class="card-body">
        <h5>2. Production settings</h5>
        <p>Keep live checkout disabled while testing. Before activation, obtain Terminal Africa's confirmation of the exact services accepting your products and all fees. Paid orders require manual shipment booking in the Terminal dashboard.</p>
        <form method="POST" action="{{ route('admin.terminal-checkout.update') }}">
            @csrf @method('PUT')
            <label for="live-package">Checkout box</label>
            <select class="form-select mb-3" id="live-package" name="package_id" required>
                <option value="">Select packaging</option>
                @foreach($packages as $package)<option value="{{ $package->id }}" @selected((string)old('package_id', $setting->terminal_checkout_package_id) === (string)$package->id)>{{ $package->name }}</option>@endforeach
            </select>
            <label for="max-units">Maximum units verified to fit this box</label>
            <input class="form-control mb-3" type="number" id="max-units" name="max_units" min="1" max="50" value="{{ old('max_units', $setting->terminal_checkout_max_units ?? 1) }}" required>
            <p class="small text-muted">Start with 1. Increase only after checking that every permitted product combination fits. Larger carts require a custom quote or pickup; automatic box selection is not included.</p>
            <label for="extra-fee">Confirmed fee excluded from the API amount (NGN)</label>
            <input class="form-control mb-3" type="number" id="extra-fee" name="extra_fee" min="0" max="100000" step="0.01" value="{{ old('extra_fee', $setting->terminal_checkout_extra_fee ?? 0) }}" required>
            <p class="small text-muted">Enter 0 if all fees are included. Enter 300 only if Terminal confirms its local fee is excluded. Markup applies to the API amount plus this fee. Priority pickup is not included.</p>
            <label for="markup">Shipping markup (%)</label>
            <input class="form-control mb-3" type="number" id="markup" name="markup" min="0" max="100" step="0.01" value="{{ old('markup', $setting->shipping_markup_percentage ?? 10) }}" required>
            <fieldset class="mb-3"><legend class="fs-6">Confirmed eligible services</legend>
                @forelse($services as $key => $label)
                    <label class="d-block"><input type="checkbox" name="services[]" value="{{ $key }}" @checked(in_array($key, old('services', $allowed), true))> {{ $label }}</label>
                @empty
                    <p>Request rates in the sandbox checkout preview, then return here to see service choices.</p>
                @endforelse
            </fieldset>
            <input type="hidden" name="confirmed" value="0">
            <label class="d-block mb-3"><input type="checkbox" name="confirmed" value="1" @checked(old('confirmed', $setting->terminal_checkout_confirmed))> I have verified box fit, product eligibility, exact ground services and all fees with Terminal Africa.</label>
            <label for="enabled">Active delivery provider</label>
            <select class="form-select mb-3" id="enabled" name="enabled">
                <option value="0" @selected(!old('enabled', $setting->terminal_checkout_enabled))>Local Delivery</option>
                <option value="1" @selected(old('enabled', $setting->terminal_checkout_enabled))>Terminal Africa — Live</option>
            </select>
            <button class="btn btn-primary">Save checkout settings</button>
        </form>
    </div></div>
</div>
@endsection
