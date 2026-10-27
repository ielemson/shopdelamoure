@extends('layouts.admin')



@section('content')

    @php

        $dispatchFields = [

            'dispatch_name' => ['Contact name', 'Abigail Ogeide', 'text'],

            'dispatch_email' => ['Contact email', 'hello@shopdelamoure.com', 'email'],

            'dispatch_phone' => ['Contact phone', '', 'tel'],

            'dispatch_line1' => ['Street address', 'No. 1, Kudirat Subair Street', 'text'],

            'dispatch_city' => ['City / locality', 'Ogombo, Ajah', 'text'],

            'dispatch_state' => ['State', 'Lagos', 'text'],

            'dispatch_postal_code' => ['Postal code', '', 'text'],

        ];

    @endphp



    <div class="body d-flex py-3">

        <div class="container-xxl">

            <h3 class="fw-bold mb-4">Delivery Settings</h3>



            @if (session('success'))

                <div class="alert alert-success" role="status">

                    {{ session('success') }}

                </div>

            @endif



            @if ($errors->any())

                <div class="alert alert-danger" role="alert">

                    <ul class="mb-0">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif



            <form id="deliverySettingsForm" action="{{ route('admin.delivery.settings.update') }}" method="POST"

                autocomplete="off">

                @csrf

                @method('PUT')



                <div class="row g-3">

                    <div class="col-lg-8">

                        <div class="card mb-3">

                            <div class="card-header py-3">

                                <h6 class="mb-0 fw-bold">Provider and Markup</h6>

                            </div>



                            <div class="card-body">

                                <label for="delivery_provider" class="form-label">

                                    Active provider

                                </label>



                                <select id="delivery_provider" name="delivery_provider" class="form-select mb-3" required>

                                    <option value="local" @selected(old('delivery_provider', $setting->delivery_provider ?? 'local') === 'local')>

                                        Local Delivery

                                    </option>

                                    <option value="terminal_africa" @selected(old('delivery_provider', $setting->delivery_provider) === 'terminal_africa')>

                                        Terminal Africa — Live

                                    </option>

                                    <option value="topship" disabled>

                                        Topship — Coming Soon

                                    </option>

                                </select>



                                <a href="{{ route('admin.shipping-rates.index') }}" class="btn btn-outline-primary mb-4">

                                    Manage Local Delivery Rates

                                </a>



                                <div>

                                    <label for="shipping_markup_percentage" class="form-label">

                                        API shipping markup (%)

                                    </label>



                                    <input type="number" id="shipping_markup_percentage" name="shipping_markup_percentage"

                                        class="form-control" min="0" max="100" step="0.01" required

                                        value="{{ old('shipping_markup_percentage', $setting->shipping_markup_percentage ?? 10) }}">



                                    <p class="small text-muted mt-2 mb-0">

                                        Saved for API shipping. Local delivery keeps its

                                        configured fees. Enter 0 for no markup.

                                    </p>

                                </div>

                            </div>

                        </div>



                        <div class="card mb-3">

                            <div class="card-header py-3">

                                <h6 class="mb-0 fw-bold">Terminal Africa</h6>

                            </div>



                            <div class="card-body">

                                <label for="terminal_environment" class="form-label">

                                    Environment

                                </label>



                                <select id="terminal_environment" name="terminal_environment" class="form-select mb-3"

                                    required>

                                    @foreach (['test' => 'Test / Sandbox', 'live' => 'Live'] as $value => $label)

                                        <option value="{{ $value }}" @selected(old('terminal_environment', $setting->terminal_environment ?? 'test') === $value)>

                                            {{ $label }}

                                        </option>

                                    @endforeach

                                </select>



                                @foreach (['test' => 'Test secret key', 'live' => 'Live secret key'] as $environment => $label)

                                    <div class="mb-3">

                                        <label for="terminal_{{ $environment }}_secret" class="form-label">

                                            {{ $label }}

                                        </label>



                                        <input type="password" id="terminal_{{ $environment }}_secret"

                                            name="terminal_{{ $environment }}_secret" class="form-control" maxlength="512"

                                            autocomplete="new-password" spellcheck="false" autocapitalize="none"

                                            placeholder="Leave blank to keep the saved key">

                                    </div>

                                @endforeach



                                <p class="small text-muted mb-0">

                                    Keys are stored encrypted and are never displayed here.

                                    Paste only the secret key, without “Bearer” or quotation

                                    marks. Leave a field blank to retain its saved key.

                                    Save changes before testing.

                                </p>

                            </div>

                        </div>



                        <div class="card mb-3">

                            <div class="card-header py-3">

                                <h6 class="mb-0 fw-bold">Dispatch Details</h6>

                            </div>



                            <div class="card-body">

                                <div class="row g-3">

                                    @foreach ($dispatchFields as $name => [$label, $default, $type])

                                        <div class="{{ $name === 'dispatch_line1' ? 'col-12' : 'col-md-6' }}">

                                            <label for="{{ $name }}" class="form-label">

                                                {{ $label }}

                                            </label>



                                            <input type="{{ $type }}" id="{{ $name }}"

                                                name="{{ $name }}" class="form-control"

                                                value="{{ old($name, $setting->{$name} ?? $default) }}" @required(!in_array($name, ['dispatch_phone', 'dispatch_postal_code'], true))>

                                        </div>

                                    @endforeach



                                    <div class="col-md-6">

                                        <label for="dispatch_country" class="form-label">

                                            Country

                                        </label>



                                        <select id="dispatch_country" name="dispatch_country" class="form-select" required>

                                            <option value="NG">Nigeria</option>

                                        </select>

                                    </div>

                                </div>



                                <p class="small text-muted mt-3 mb-0">

                                    Add the dispatch phone and confirm the postal code

                                    before shipment testing. The locality will be checked

                                    against Terminal Africa's location data during rate

                                    integration.

                                </p>

                            </div>

                        </div>



                        
<div class="card mb-3"><div class="card-header"><h6>Terminal Africa Checkout</h6></div>
<div class="card-body">
<label class="form-label" for="terminal_checkout_package_id">Checkout box</label>
<select class="form-select mb-3" id="terminal_checkout_package_id" name="terminal_checkout_package_id">
<option value="">Select packaging</option>
@foreach($packages as $package)
<option value="{{ $package->id }}" @selected((string) old('terminal_checkout_package_id', $setting->terminal_checkout_package_id) === (string) $package->id)>{{ $package->name }}</option>
@endforeach
</select>
<label class="form-label">Maximum units verified to fit this box</label>
<input class="form-control mb-3" type="number" min="1" max="100" name="terminal_checkout_max_units" value="{{ old('terminal_checkout_max_units', $setting->terminal_checkout_max_units ?: 1) }}">
<p class="small text-muted">Start with 1. Increase only after verifying every permitted product combination fits.</p>
<label class="form-label">Confirmed fee excluded from API amount (NGN)</label>
<input class="form-control mb-3" type="number" min="0" max="100000" step="0.01" name="terminal_checkout_extra_fee" value="{{ old('terminal_checkout_extra_fee', $setting->terminal_checkout_extra_fee ?? 0) }}">
<p class="small text-muted">Use 0 when included. Use 300 only if Terminal confirms it is excluded. Your shipping markup applies to the API amount plus this fee.</p>
<label class="form-label">Approved delivery services</label>
@forelse($serviceChoices as $key => $label)
<div class="form-check"><input class="form-check-input" type="checkbox" name="terminal_checkout_services[]" id="service-{{ $key }}" value="{{ $key }}" @checked(in_array($key, old('terminal_checkout_services', $approvedServices), true))><label class="form-check-label" for="service-{{ $key }}">{{ $label }}</label></div>
@empty
<p>Run a sandbox checkout preview below, request rates, then return here to select the exact services confirmed by Terminal.</p>
@endforelse
<input type="hidden" name="terminal_checkout_confirmed" value="0">
<label class="d-block mt-3"><input type="checkbox" name="terminal_checkout_confirmed" value="1" @checked(old('terminal_checkout_confirmed', $setting->terminal_checkout_confirmed))> I have verified box fit, product eligibility, exact services and all fees with Terminal Africa.</label>
<input type="hidden" name="terminal_auto_book" value="0">
<label class="d-block mt-3"><input type="checkbox" name="terminal_auto_book" value="1" @checked(old('terminal_auto_book', $setting->terminal_auto_book))> Book delivery automatically after verified payment and successful stock allocation.</label>
<p class="small text-muted">Booking charges your Terminal wallet. Failed or uncertain bookings appear in Delivery Operations for review.</p>
<a class="btn btn-outline-primary mt-3" href="{{ url('/admin/terminal-delivery') }}">Delivery Operations</a>
</div></div>

<button type="submit" class="btn btn-primary mb-3">

                            <i class="icofont-save me-2"></i>

                            Save Changes

                        </button>

                    </div>



                    <div class="col-lg-4">

                        <div class="card mb-3">

                            <div class="card-header py-3">

                                <h6 class="mb-0 fw-bold">Store Pickup</h6>

                            </div>



                            <div class="card-body">

                                <p>

                                    Store pickup is managed in Website Settings.

                                </p>



                                <a href="{{ route('admin.website.settings') }}" class="btn btn-outline-primary">

                                    Open Website Settings

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </form>



            
<div class="card my-3"><div class="card-body"><h6>Sandbox checkout preview</h6>
<p>Only this browser session uses sandbox rates for 30 minutes. No payment or shipment booking is permitted in preview.</p>
<form method="POST" action="{{ url('/admin/terminal-delivery/preview') }}">@csrf
<select name="package_id" class="form-select mb-2" required>@foreach($packages as $package)<option value="{{ $package->id }}">{{ $package->name }}</option>@endforeach</select>
<button class="btn btn-outline-primary">Start sandbox preview</button></form>
<form method="POST" action="{{ url('/admin/terminal-delivery/preview/end') }}" class="mt-2">@csrf<button class="btn btn-outline-secondary">End preview</button></form>
<p class="small mt-2">Add one item, quantity 1, with its actual shipping weight saved. Open checkout, request rates, then return to these settings.</p>
</div></div>

<div class="card mt-3">

                <div class="card-body">

                    <h6 class="fw-bold">Test Saved Connection</h6>



                    <p class="small text-muted">

                        Uses the saved environment and key to read your account's

                        carrier list. It does not create an address or book a shipment.

                    </p>



                    <form id="terminalConnectionForm" action="{{ route('admin.delivery.settings.test') }}" method="POST">

                        @csrf



                        <button type="submit" id="terminalTestButton" class="btn btn-outline-primary">

                            Test Connection

                        </button>

                    </form>



                    <p id="terminalTestResult" class="mt-3 mb-0" role="status" aria-live="polite"></p>

                </div>

            </div>

        </div>

    </div>



    <script>

        (() => {

            const settingsForm = document.getElementById('deliverySettingsForm');

            const connectionForm = document.getElementById('terminalConnectionForm');

            const button = document.getElementById('terminalTestButton');

            const result = document.getElementById('terminalTestResult');



            if (!settingsForm || !connectionForm || !button || !result) {

                return;

            }



            let settingsChanged = false;



            const markSettingsChanged = () => {

                settingsChanged = true;

            };



            settingsForm.addEventListener('input', markSettingsChanged);

            settingsForm.addEventListener('change', markSettingsChanged);



            connectionForm.addEventListener('submit', async function(event) {

                event.preventDefault();



                result.className = 'mt-3 mb-0';



                if (settingsChanged) {

                    result.classList.add('text-danger');

                    result.textContent =

                        'You have unsaved changes. Click Save Changes, then test the saved connection.';

                    return;

                }



                const csrfToken = this.querySelector('[name="_token"]');



                if (!csrfToken) {

                    result.classList.add('text-danger');

                    result.textContent =

                        'Session token is missing. Refresh the page and try again.';

                    return;

                }



                button.disabled = true;

                result.textContent = 'Testing saved configuration...';



                try {

                    const response = await fetch(this.action, {

                        method: 'POST',

                        credentials: 'same-origin',

                        headers: {

                            'Accept': 'application/json',

                            'X-CSRF-TOKEN': csrfToken.value

                        }

                    });



                    if (response.status === 419) {

                        result.classList.add('text-danger');

                        result.textContent =

                            'Your session has expired. Refresh the page and try again.';

                        return;

                    }



                    if (response.redirected || response.status === 401) {

                        result.classList.add('text-danger');

                        result.textContent =

                            'Your admin session may have expired. Refresh the page and sign in again if prompted.';

                        return;

                    }



                    const contentType = response.headers.get('content-type') || '';



                    if (!contentType.includes('application/json')) {

                        throw new Error('Unexpected response format.');

                    }



                    const data = await response.json();

                    const success = response.ok && data.success === true;



                    result.classList.add(

                        success ? 'text-success' : 'text-danger'

                    );



                    result.textContent =

                        data.message || 'Connection test could not be completed.';

                } catch (error) {

                    result.classList.add('text-danger');

                    result.textContent =

                        'Unable to complete the test. Refresh the page and try again.';

                } finally {

                    button.disabled = false;

                }

            });

        })();

    </script>

@endsection
