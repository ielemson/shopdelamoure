@if($terminalMode)
<div id="terminalCheckoutFields" class="mb-7">
    <label for="terminalCity" class="form-label">Delivery city</label>
    <select id="terminalCity" name="terminal_city" class="form-select mb-3"><option value="">Select your state first</option></select>
    <label for="terminalZip" class="form-label">Postal code (optional for quote)</label>
    <input type="text" id="terminalZip" name="terminal_zip" maxlength="20" class="form-control mb-3" value="{{ old('terminal_zip') }}">
    <button type="button" id="terminalGetRates" class="btn btn-outline-primary mb-3">Get delivery rates</button>
    <label for="terminalRate" class="form-label d-block">Delivery service</label>
    <select id="terminalRate" name="terminal_rate_id" class="form-select" disabled><option value="">Request rates first</option></select>
    <input type="hidden" id="terminalQuoteToken" name="terminal_quote_token">
    <p id="terminalNotice" class="small mt-2" role="status" aria-live="polite"></p>
</div>
@endif
