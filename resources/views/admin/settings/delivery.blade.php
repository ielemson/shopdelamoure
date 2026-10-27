@extends('layouts.admin')
@section('content')
<div class="container-xxl py-4">
<h3 class="mb-4">Delivery Settings</h3>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form id="deliverySettingsForm" action="{{ route('admin.delivery.settings.update') }}" method="POST" autocomplete="off">
@csrf @method('PUT')
<div class="row g-4"><div class="col-lg-7">
<div class="card mb-4"><div class="card-body"><h5>Delivery provider</h5>
<select name="delivery_provider" class="form-select mb-3" required>
<option value="local" @selected(old('delivery_provider', $setting->delivery_provider) === 'local')>Local Delivery</option>
<option value="terminal_africa" @selected(old('delivery_provider', $setting->delivery_provider) === 'terminal_africa')>Terminal Africa — Live</option>
</select>
<label for="terminal_live_secret" class="form-label">Live secret key</label>
<input id="terminal_live_secret" name="terminal_live_secret" type="password" class="form-control mb-3" maxlength="512" autocomplete="new-password" placeholder="Leave blank to keep the saved key">
<p class="small text-muted">Stored encrypted. Enter the secret key only, without “Bearer”.</p>
<label class="form-label">Shipping markup (%)</label><input name="shipping_markup_percentage" type="number" min="0" max="100" step="0.01" class="form-control mb-3" value="{{ old('shipping_markup_percentage', $setting->shipping_markup_percentage ?? 0) }}" required>
<label class="form-label">Additional delivery charge (NGN, optional)</label><input name="terminal_checkout_extra_fee" type="number" min="0" max="100000" step="0.01" class="form-control mb-3" value="{{ old('terminal_checkout_extra_fee', $setting->terminal_checkout_extra_fee ?? 0) }}">
<p class="small text-muted">Leave 0 unless you need a charge outside the API rate. Markup applies to the rate plus this amount.</p>
<input type="hidden" name="terminal_auto_book" value="0">
<label><input type="checkbox" name="terminal_auto_book" value="1" @checked(old('terminal_auto_book', $setting->terminal_auto_book))> Automatically book after successful payment and stock allocation</label>
</div></div>
<div class="card mb-4"><div class="card-body"><h5>Pickup address</h5><div class="row g-3">
@foreach(['dispatch_name' => ['Sender first and last name', 'text'], 'dispatch_email' => ['Email (optional)', 'email'], 'dispatch_phone' => ['Phone', 'tel'], 'dispatch_line1' => ['Street address', 'text'], 'dispatch_city' => ['City', 'text'], 'dispatch_state' => ['State', 'text'], 'dispatch_postal_code' => ['Postal code', 'text']] as $name => [$label, $type])
<div class="{{ $name === 'dispatch_line1' ? 'col-12' : 'col-md-6' }}"><label for="{{ $name }}" class="form-label">{{ $label }}</label><input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="form-control" maxlength="255" value="{{ old($name, $setting->{$name}) }}"></div>
@endforeach
</div><p class="small text-muted mt-3 mb-0">Country: Nigeria.</p></div></div>
<button class="btn btn-primary" type="submit">Save delivery settings</button>
</div><div class="col-lg-5">
<div class="card mb-4"><div class="card-body"><h5>Parcel packaging</h5>
<p class="small text-muted">Used for parcel dimensions and packaging weight. Product weights come from your catalog.</p>
<select class="form-select" name="terminal_checkout_package_id"><option value="">Use first active packaging preset</option>@foreach($packages as $package)<option value="{{ $package->id }}" @selected((string) old('terminal_checkout_package_id', $setting->terminal_checkout_package_id) === (string) $package->id)>{{ $package->name }}</option>@endforeach</select>
@if($packages->isEmpty())<p class="text-danger mt-2">Add one active packaging preset before activating delivery.</p>@endif
</div></div>
<div class="card mb-4"><div class="card-body"><h5>Active Terminal carriers</h5>
@if($carrierError)<p class="text-muted">{{ $carrierError }}</p>@endif
<ul>@forelse($carriers as $carrier)<li>{{ $carrier['name'] ?? 'Carrier' }}</li>@empty<li>No carrier list loaded.</li>@endforelse</ul>
<p class="small text-muted mb-0">Manage enabled carriers in your Terminal Africa dashboard. Available rates are calculated automatically at checkout.</p>
</div></div>
<a class="btn btn-outline-primary" href="{{ url('/admin/terminal-delivery') }}">Delivery Operations</a>
<a class="btn btn-outline-secondary mt-2" href="{{ route('admin.shipping-rates.index') }}">Local Delivery Rates</a>
</div></div>
</form>
<div class="card mt-4"><div class="card-body"><form id="terminalConnectionForm" action="{{ route('admin.delivery.settings.test') }}" method="POST">@csrf<button id="terminalTestButton" class="btn btn-outline-primary">Test live connection</button></form><p id="terminalTestResult" class="mt-3 mb-0" role="status" aria-live="polite"></p></div></div>
</div>
<script>
(()=>{
 const form=document.getElementById('deliverySettingsForm');
 const test=document.getElementById('terminalConnectionForm');
 const result=document.getElementById('terminalTestResult');
 const button=document.getElementById('terminalTestButton');
 let changed=false;
 form.addEventListener('input',()=>changed=true);
 form.addEventListener('change',()=>changed=true);
 test.addEventListener('submit',async function(event){
  event.preventDefault();
  if(changed){result.textContent='Save delivery settings before testing.';return;}
  button.disabled=true; result.textContent='Testing live connection...';
  try{
   const response=await fetch(this.action,{method:'POST',credentials:'same-origin',headers:{'Accept':'application/json','X-CSRF-TOKEN':this.querySelector('[name="_token"]').value}});
   const data=await response.json();result.textContent=data.message||'Connection could not be completed.';
   result.className='mt-3 mb-0 '+(response.ok&&data.success?'text-success':'text-danger');
  }catch(error){result.textContent='Refresh the page and check your admin session or connectivity.';}
  finally{button.disabled=false;}
 });
})();
</script>
@endsection
