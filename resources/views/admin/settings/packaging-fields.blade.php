@php
    $packageFields = [
        'name' => ['Preset name', 'text', null, null],
        'length_cm' => ['Outer length (cm)', 'number', '0.01', '99999999.99'],
        'width_cm' => ['Outer width (cm)', 'number', '0.01', '99999999.99'],
        'height_cm' => ['Outer height (cm)', 'number', '0.01', '99999999.99'],
        'empty_weight_kg' => ['Box and dispatch padding weight (kg)', 'number', '0.001', '9999999.999'],
    ];
    $restorePackage = old('_package_form') === $packagePrefix;
@endphp
<input type="hidden" name="_package_form" value="{{ $packagePrefix }}">
<div class="row g-3">
    @foreach ($packageFields as $field => [$label, $type, $step, $maximum])
        <div class="col-md-6">
            <label for="{{ $packagePrefix }}-{{ $field }}" class="form-label">{{ $label }}</label>
            <input id="{{ $packagePrefix }}-{{ $field }}" type="{{ $type }}" name="{{ $field }}" class="form-control" required
                value="{{ $restorePackage ? old($field) : data_get($packageItem, $field) }}"
                @if ($step) step="{{ $step }}" min="{{ $step }}" max="{{ $maximum }}" @else maxlength="150" @endif>
        </div>
    @endforeach
    <div class="col-md-6">
        <label for="{{ $packagePrefix }}-active" class="form-label">Status</label>
        <select id="{{ $packagePrefix }}-active" name="is_active" class="form-select">
            @php($packageActive = $restorePackage ? old('is_active', 1) : data_get($packageItem, 'is_active', true))
            <option value="1" @selected((bool) $packageActive)>Active</option>
            <option value="0" @selected(!(bool) $packageActive)>Inactive</option>
        </select>
    </div>
</div>
