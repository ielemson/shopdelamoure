@if($terminalMode)
<div class="row g-3 my-2">
<div class="col-md-6"><label for="terminalCity">Delivery city</label><select id="terminalCity" name="terminal_city" class="form-select"><option value="">Select your state first</option></select></div>
<div class="col-md-6"><label for="terminalZip">Postal code</label><input id="terminalZip" name="terminal_zip" class="form-control" maxlength="20" value="{{ old('terminal_zip') }}"></div>
<div class="col-12"><button type="button" id="terminalGetRates" class="btn btn-outline-primary">Get delivery rates</button></div>
<div class="col-12"><label for="terminalRate">Delivery service</label><select id="terminalRate" name="terminal_rate_id" class="form-select" disabled><option value="">Request rates first</option></select></div>
<input type="hidden" id="terminalQuoteToken" name="terminal_quote_token" value="">
<p id="terminalNotice" role="status" aria-live="polite"></p>
</div>
@endif
