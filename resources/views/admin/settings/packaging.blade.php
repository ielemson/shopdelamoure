@extends('layouts.admin')

@section('content')
<div class="body d-flex py-3">
    <div class="container-xxl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Packaging & Sandbox Quotes</h3>
            <a href="{{ route('admin.delivery.settings') }}" class="btn btn-outline-primary">Delivery Settings</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul></div>
        @endif
        <p>Save the actual boxes you use. Measure outer dimensions and weigh the empty box with its usual dispatch padding. Item weights are recorded separately.</p>
        <div class="card mb-3">
            <div class="card-header"><h6 class="fw-bold mb-0">Add Packaging Preset</h6></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.shipping-packages.store') }}">
                    @csrf
                    @include('admin.settings.packaging-fields', ['packagePrefix' => 'new-package', 'packageItem' => null])
                    <button type="submit" class="btn btn-primary mt-3">Save Preset</button>
                </form>
            </div>
        </div>
        @foreach ($packages as $package)
            <details class="card mb-3">
                <summary class="card-header">{{ $package->name }} — {{ $package->is_active ? 'Active' : 'Inactive' }}</summary>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shipping-packages.update', $package) }}">
                        @csrf
                        @method('PUT')
                        @include('admin.settings.packaging-fields', ['packagePrefix' => 'package-'.$package->id, 'packageItem' => $package])
                        <button type="submit" class="btn btn-primary mt-3">Update Preset</button>
                    </form>
                </div>
            </details>
        @endforeach
        <div class="card mt-4">
            <div class="card-header"><h6 class="fw-bold mb-0">Single-item Sandbox Quote Test</h6></div>
            <div class="card-body">
                <p>This creates test packaging, address and parcel records in Terminal Africa's sandbox. It does not book delivery. Choose a box that physically fits the item.</p>
                <form id="packageQuoteForm" method="POST" action="{{ route('admin.shipping-packages.quote') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="quote-package" class="form-label">Saved box</label>
                            <select id="quote-package" name="package_id" class="form-select" required>
                                <option value="">Select packaging</option>
                                @foreach ($packages->where('is_active', true) as $package)
                                    <option value="{{ $package->id }}">{{ $package->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @foreach ([
                            'item_description' => ['Item description', 'text', null],
                            'item_weight_kg' => ['One item weight, excluding dispatch box (kg)', 'number', '0.001'],
                            'item_value_ngn' => ['Item value (NGN)', 'number', '0.01'],
                            'city' => ['Destination city', 'text', null],
                            'state' => ['Destination state', 'text', null],
                            'zip' => ['Destination postal code (optional for this quote test)', 'text', null],
                        ] as $field => [$label, $type, $step])
                            <div class="col-md-6">
                                <label for="quote-{{ $field }}" class="form-label">{{ $label }}</label>
                                <input id="quote-{{ $field }}" name="{{ $field }}" type="{{ $type }}" class="form-control"
                                    @required($field !== 'zip') @if ($step) step="{{ $step }}" min="{{ $step }}" @endif>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" id="quoteButton" class="btn btn-primary mt-3">Fetch Sandbox Rates</button>
                </form>
                <div id="quoteMessage" class="mt-3" role="status" aria-live="polite"></div>
                <div class="table-responsive mt-3">
                    <table class="table">
                        <thead><tr><th>Carrier / service</th><th>Estimate</th><th>API quote (₦)</th><th>Markup (₦)</th><th>Preview charge (₦)</th></tr></thead>
                        <tbody id="quoteRows"></tbody>
                    </table>
                </div>
                <p class="small text-muted">Diagnostic results may include services that cannot carry your fragrances. Carrier acceptance, ground transport and any additional platform, insurance or pickup fees must be confirmed before checkout activation. Markup uses the saved admin percentage and only the returned quote amount.</p>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('packageQuoteForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    const button = document.getElementById('quoteButton');
    const message = document.getElementById('quoteMessage');
    const rows = document.getElementById('quoteRows');
    button.disabled = true;
    rows.replaceChildren();
    message.textContent = 'Requesting sandbox rates...';
    try {
        const response = await fetch(this.action, {
            method: 'POST', credentials: 'same-origin',
            headers: {'Accept': 'application/json'}, body: new FormData(this)
        });
        const data = await response.json();
        if (!response.ok || data.success !== true) {
            const validation = data.errors ? Object.values(data.errors).flat().join(' ') : '';
            throw new Error(validation || data.message || 'Quote test failed.');
        }
        message.textContent = data.message + ' Estimated total weight: ' + data.estimated_total_weight_kg
            + ' kg. Markup: ' + data.markup_percentage + '%.';
        const money = value => Number(value).toLocaleString('en-NG', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        for (const rate of data.rates) {
            const row = document.createElement('tr');
            for (const value of [rate.carrier + ' / ' + rate.service, rate.delivery_time,
                money(rate.quoted_amount), money(rate.markup), money(rate.customer_preview)]) {
                const cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            }
            rows.appendChild(row);
        }
    } catch (error) {
        message.textContent = error.message || 'Unable to complete sandbox quote test.';
    } finally {
        button.disabled = false;
    }
});
</script>
@endsection
