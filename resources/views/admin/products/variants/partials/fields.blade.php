@php
    $useOld = old('_variant_form') === $prefix;
    $value = fn($field, $default = null) => $useOld
        ? old($field, $default)
        : ($variant
            ? $variant->{$field}
            : $default);

    if ($useOld) {
        $keys = old('option_keys', []);
        $values = old('option_values', []);
        $keys = is_array($keys) ? array_values($keys) : [];
        $values = is_array($values) ? array_values($values) : [];
        $options = [];
        for ($i = 0; $i < max(count($keys), count($values)); $i++) {
            $options[] = [$keys[$i] ?? '', $values[$i] ?? ''];
        }
    } else {
        $options = collect($variant?->options ?? [])
            ->map(fn($optionValue, $optionKey) => [$optionKey, $optionValue])
            ->values()
            ->all();
    }
    if (empty($options)) {
        $options = [['', '']];
    }

    $wholesaleTiers = $useOld
        ? old('wholesale_tiers', [])
        : ($variant
            ? $variant->wholesalePriceTiers
                ->map(
                    fn($tier) => [
                        'min_quantity' => $tier->min_quantity,
                        'price_ngn' => $tier->price_ngn,
                    ],
                )
                ->all()
            : []);
    $wholesaleTiers = is_array($wholesaleTiers) ? array_values(array_filter($wholesaleTiers, 'is_array')) : [];
    $wholesaleMinimum = (int) ($product->wholesale_min_quantity ?? 12);
@endphp

<input type="hidden" name="_variant_form" value="{{ $prefix }}">
<input type="hidden" name="_wholesale_form" value="1">

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
        <small class="text-muted">Examples: Size → 200 ml; Scent → Vanilla; Colour → Black.</small>
    </div>

    <div class="col-md-6">
        <label class="form-label" for="{{ $prefix }}-image">Variant Image</label>
        <input id="{{ $prefix }}-image" type="file" name="image" accept="image/*" class="form-control">
        @if ($variant?->image)
            <img src="{{ asset($variant->image) }}" alt="{{ $variant->name }}" width="70" height="70"
                class="rounded mt-2" style="object-fit: cover;">
            <div class="form-check mt-2">
                <input type="hidden" name="remove_image" value="0">
                <input type="checkbox" name="remove_image" value="1" class="form-check-input"
                    id="{{ $prefix }}-remove-image" @checked((bool) $value('remove_image', false))>
                <label class="form-check-label" for="{{ $prefix }}-remove-image">Remove current image</label>
            </div>
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

    <div class="col-12">
        <div class="border rounded p-3" data-variant-wholesale data-minimum="{{ $wholesaleMinimum }}">
            <h6 class="fw-bold mb-3">Wholesale Pricing</h6>
            <input type="hidden" name="wholesale_enabled" value="0">
            <div class="form-check form-switch mb-3">
                <input type="checkbox" name="wholesale_enabled" value="1" class="form-check-input"
                    id="{{ $prefix }}-wholesale-enabled" data-wholesale-enabled @checked((bool) $value('wholesale_enabled', false))>
                <label class="form-check-label" for="{{ $prefix }}-wholesale-enabled">
                    Enable wholesale for this variant
                </label>
            </div>
            <p class="small text-muted">
                Minimum wholesale quantity: <strong>{{ $wholesaleMinimum }} units</strong>.
                @if ($product->wholesale_allow_mixed_variants)
                    Eligible variants of this product combine towards the minimum and discount quantities.
                @else
                    Each variant must meet the minimum quantity independently.
                @endif
                Each variant uses its own wholesale prices.
            </p>
            @if (!$product->wholesale_enabled)
                <p class="small text-muted">
                    You can configure prices now. Enable wholesale on the parent product to make them available to
                    customers.
                </p>
            @endif
            <div class="mb-3">
                <label for="{{ $prefix }}-wholesale-price" class="form-label">Base Wholesale Unit Price
                    (NGN)</label>
                <div class="input-group">
                    <span class="input-group-text">₦</span>
                    <input id="{{ $prefix }}-wholesale-price" type="number" name="wholesale_price_ngn"
                        min="0.01" max="9999999999999.99" step="0.01" class="form-control"
                        data-wholesale-base-price value="{{ $value('wholesale_price_ngn') }}"
                        {{ $value('wholesale_enabled', false) || count($wholesaleTiers) ? 'required' : '' }}>
                </div>
                @if ($useOld)
                    @error('wholesale_price_ngn')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                @endif
            </div>
            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                <h6 class="mb-0">Additional Quantity Discounts</h6>
                <button type="button" class="btn btn-outline-primary btn-sm" data-add-wholesale-tier>Add
                    Tier</button>
            </div>
            <p class="small text-muted">
                Additional tier quantities must exceed {{ $wholesaleMinimum }} units.
                Each higher quantity tier must have a lower unit price.
            </p>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Quantity From</th>
                            <th scope="col">Unit Price (₦)</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody data-wholesale-tier-rows>
                        @foreach ($wholesaleTiers as $index => $tier)
                            <tr data-wholesale-tier>
                                <td>
                                    <input type="number" name="wholesale_tiers[{{ $index }}][min_quantity]"
                                        min="{{ $wholesaleMinimum + 1 }}" max="4294967295" step="1" required
                                        class="form-control" data-tier-quantity aria-label="Tier minimum quantity"
                                        value="{{ $tier['min_quantity'] ?? '' }}">
                                    @if ($useOld)
                                        @error("wholesale_tiers.$index.min_quantity")
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </td>
                                <td>
                                    <input type="number" name="wholesale_tiers[{{ $index }}][price_ngn]"
                                        min="0.01" max="9999999999999.99" step="0.01" required
                                        class="form-control" data-tier-price aria-label="Tier unit price in naira"
                                        value="{{ $tier['price_ngn'] ?? '' }}">
                                    @if ($useOld)
                                        @error("wholesale_tiers.$index.price_ngn")
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        data-remove-wholesale-tier>Remove</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p class="small text-muted mt-2 mb-0" data-wholesale-empty @if (count($wholesaleTiers)) hidden @endif>
                No additional tiers. The base wholesale price applies to all qualifying quantities.
            </p>
            <template data-wholesale-tier-template>
                <tr data-wholesale-tier>
                    <td>
                        <input type="number" class="form-control" data-tier-quantity
                            aria-label="Tier minimum quantity" min="{{ $wholesaleMinimum + 1 }}" max="4294967295"
                            step="1" required>
                    </td>
                    <td>
                        <input type="number" class="form-control" data-tier-price
                            aria-label="Tier unit price in naira" min="0.01" max="9999999999999.99"
                            step="0.01" required>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                            data-remove-wholesale-tier>Remove</button>
                    </td>
                </tr>
            </template>
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-stock_quantity">Quantity</label>
        <input id="{{ $prefix }}-stock_quantity" type="number" name="stock_quantity" min="0" required
            class="form-control" value="{{ $value('stock_quantity', 0) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-low_stock_threshold">Low Stock Alert</label>
        <input id="{{ $prefix }}-low_stock_threshold" type="number" name="low_stock_threshold"
            min="0" required class="form-control" value="{{ $value('low_stock_threshold', 5) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label" for="{{ $prefix }}-stock_status">Stock Status</label>
        <select id="{{ $prefix }}-stock_status" name="stock_status" class="form-select">
            <option value="in_stock" @selected($value('stock_status', 'out_of_stock') === 'in_stock')>In Stock</option>
            <option value="out_of_stock" @selected($value('stock_status', 'out_of_stock') === 'out_of_stock')>Out of Stock</option>
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
                <label class="form-check-label"
                    for="{{ $prefix }}-{{ $field }}">{{ $label }}</label>
            </div>
        @endforeach
    </div>
</div>

@once
    <script>
        (() => {
            function initialiseWholesaleForms() {
                document.querySelectorAll('[data-variant-wholesale]').forEach(root => {
                    if (root.dataset.initialised === '1') return;
                    root.dataset.initialised = '1';
                    const rows = root.querySelector('[data-wholesale-tier-rows]');
                    const template = root.querySelector('[data-wholesale-tier-template]');
                    const addButton = root.querySelector('[data-add-wholesale-tier]');
                    const basePrice = root.querySelector('[data-wholesale-base-price]');
                    const enabled = root.querySelector('[data-wholesale-enabled]');
                    const emptyNotice = root.querySelector('[data-wholesale-empty]');
                    const minimum = Number(root.dataset.minimum);

                    function refresh() {
                        const tiers = Array.from(rows.querySelectorAll('[data-wholesale-tier]'));
                        tiers.forEach((row, index) => {
                            row.querySelector('[data-tier-quantity]').name =
                                `wholesale_tiers[${index}][min_quantity]`;
                            row.querySelector('[data-tier-price]').name =
                                `wholesale_tiers[${index}][price_ngn]`;
                        });
                        basePrice.required = enabled.checked || tiers.length > 0;
                        emptyNotice.hidden = tiers.length > 0;
                        addButton.disabled = tiers.length >= 50 || minimum >= 4294967295;
                    }
                    addButton.addEventListener('click', () => {
                        if (addButton.disabled) return;
                        rows.appendChild(template.content.cloneNode(true));
                        refresh();
                        rows.lastElementChild.querySelector('[data-tier-quantity]').focus();
                    });
                    rows.addEventListener('click', event => {
                        const button = event.target.closest('[data-remove-wholesale-tier]');
                        if (!button || !rows.contains(button)) return;
                        button.closest('[data-wholesale-tier]').remove();
                        refresh();
                    });
                    enabled.addEventListener('change', refresh);
                    root.closest('form').addEventListener('submit', refresh);
                    refresh();
                });
            }
            // Defer until all shared partial instances have been parsed.
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initialiseWholesaleForms, {
                    once: true
                });
            } else {
                initialiseWholesaleForms();
            }
        })
        ();
    </script>
@endonce
