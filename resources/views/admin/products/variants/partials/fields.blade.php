@php
    $useOld = old('_variant_form') === $prefix;
    $value = fn($field, $default = null) => $useOld
        ? old($field, $default)
        : ($variant
            ? $variant->{$field}
            : $default);

    $options = $useOld
        ? array_map(null, old('option_keys', []), old('option_values', []))
        : collect($variant?->options ?? [])
            ->map(fn($optionValue, $optionKey) => [$optionKey, $optionValue])
            ->values()
            ->all();

    if (empty($options)) {
        $options = [['', '']];
    }
@endphp

<input type="hidden" name="_variant_form" value="{{ $prefix }}">

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-name">Variant Name *</label>
        <input id="{{ $prefix }}-name" type="text" name="name" class="form-control" required
            value="{{ $value('name') }}" placeholder="e.g. 200 ml, 400 ml">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-sku">Variant SKU</label>
        <input id="{{ $prefix }}-sku" type="text" name="sku" class="form-control"
            value="{{ $value('sku') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Options</label>
        <div class="row g-2">
            @foreach ($options as [$key, $optionValue])
                <div class="col-md-6">
                    <input name="option_keys[]" class="form-control" value="{{ $key }}"
                        placeholder="Option name, e.g. Size">
                </div>
                <div class="col-md-6">
                    <input name="option_values[]" class="form-control" value="{{ $optionValue }}"
                        placeholder="Value, e.g. 200 ml">
                </div>
            @endforeach

            @for ($i = count($options); $i < 4; $i++)
                <div class="col-md-6">
                    <input name="option_keys[]" class="form-control" placeholder="Option name">
                </div>
                <div class="col-md-6">
                    <input name="option_values[]" class="form-control" placeholder="Value">
                </div>
            @endfor
        </div>
        <small class="text-muted">
            Examples: Size → 200 ml; Scent → Vanilla; Colour → Black.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-image">Variant Image</label>
        <input id="{{ $prefix }}-image" type="file" name="image" accept="image/*" class="form-control">
        @if ($variant?->image)
            <img src="{{ asset($variant->image) }}" alt="{{ $variant->name }}" width="70" height="70"
                class="rounded mt-2" style="object-fit: cover;">
        @endif
    </div>

    @foreach ([
        'price_ngn' => 'Regular Price (NGN)',
        'sale_price_ngn' => 'Sale Price (NGN)',
    ] as $field => $label)
        <div class="col-md-3">
            <label class="form-label" for="{{ $prefix }}-{{ $field }}">{{ $label }}</label>
            <input id="{{ $prefix }}-{{ $field }}" type="number" name="{{ $field }}"
                step="0.01" min="0" class="form-control" value="{{ $value($field) }}">
        </div>
    @endforeach

    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-stock_quantity">Quantity</label>
        <input id="{{ $prefix }}-stock_quantity" type="number" name="stock_quantity" min="0" required
            class="form-control" value="{{ $value('stock_quantity', 0) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-low_stock_threshold">Low Stock Alert</label>
        <input id="{{ $prefix }}-low_stock_threshold" type="number" name="low_stock_threshold" min="0"
            required class="form-control" value="{{ $value('low_stock_threshold', 5) }}">
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-stock_status">Stock Status</label>
        <select id="{{ $prefix }}-stock_status" name="stock_status" class="form-select">
            <option value="in_stock" @selected($value('stock_status', 'out_of_stock') === 'in_stock')>
                In Stock
            </option>
            <option value="out_of_stock" @selected($value('stock_status', 'out_of_stock') === 'out_of_stock')>
                Out of Stock
            </option>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-sort_order">Sort Order</label>
        <input id="{{ $prefix }}-sort_order" type="number" name="sort_order" min="0" required
            class="form-control" value="{{ $value('sort_order', 0) }}">
    </div>

    <div class="col-12 d-flex flex-wrap gap-4">
        @foreach ([
        'track_stock' => 'Track Stock',
        'is_active' => 'Active',
        'is_default' => 'Default Variant',
    ] as $field => $label)
            <div class="form-check">
                <input type="hidden" name="{{ $field }}" value="0">
                <input id="{{ $prefix }}-{{ $field }}" type="checkbox" name="{{ $field }}"
                    value="1" class="form-check-input" @checked((bool) $value($field, $field !== 'is_default'))>
                <label class="form-check-label" for="{{ $prefix }}-{{ $field }}">
                    {{ $label }}
                </label>
            </div>
        @endforeach
    </div>
</div>
