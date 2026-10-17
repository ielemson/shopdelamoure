@extends('layouts.app')

@section('meta_title', $product->name . ' Wholesale | Dela Moure')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($product->short_description ??
    ($product->description ?? 'Explore Dela Moure wholesale prices and quantity discounts.')), 155))

@section('PageContent')
    @php
        $minimum = (int) $product->wholesale_min_quantity;
        $mixed = $product->has_variants && $product->wholesale_allow_mixed_variants;
        $variants = $product->activeVariants;
        $galleryImages = collect();
        if ($product->main_image) {
            $galleryImages->push(['image' => $product->main_image, 'alt' => $product->name]);
        }
        foreach ($product->images as $image) {
            if ($image->image && !$galleryImages->contains(fn($item) => $item['image'] === $image->image)) {
                $galleryImages->push(['image' => $image->image, 'alt' => $image->alt_text ?: $product->name]);
            }
        }
        foreach ($variants as $variant) {
            if ($variant->image && !$galleryImages->contains(fn($item) => $item['image'] === $variant->image)) {
                $galleryImages->push(['image' => $variant->image, 'alt' => $variant->name]);
            }
        }
        $firstImage = $galleryImages->first();
        $orderingAvailable = \Illuminate\Support\Facades\Route::has('wholesale.cart.add');
        $orderUrl = $orderingAvailable ? route('wholesale.cart.add') : '';
        $sources = $product->has_variants ? $variants : collect([$product]);
        $priceData = $sources
            ->map(function ($source) use ($product) {
                return [
                    'id' => $product->has_variants ? (string) $source->id : 'simple',
                    'base' => (string) $source->wholesale_price_ngn,
                    'tiers' => $source->wholesalePriceTiers
                        ->map(
                            fn($tier) => [
                                'minimum' => (int) $tier->min_quantity,
                                'price' => (string) $tier->price_ngn,
                            ],
                        )
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    @endphp

    <section class="pb-lg-20 pb-16">
        @include('frontend.partials.breadcrumb', ['title' => 'Wholesale', 'item' => $product->name])
    </section>
    <section class="container pt-6 pb-14 pb-lg-20">
        <a href="{{ url('/wholesale') }}" class="d-inline-block text-muted mb-6">← All Wholesale Products</a>
        <div class="row">
            <div class="col-md-6 pe-lg-13">
                <a id="wholesale-main-image-link"
                    href="{{ asset($firstImage['image'] ?? 'assets/images/products/product-placeholder.jpg') }}"
                    data-gallery="wholesale-gallery" class="d-block">
                    <img id="wholesale-main-image"
                        src="{{ asset($firstImage['image'] ?? 'assets/images/products/product-placeholder.jpg') }}"
                        alt="{{ $firstImage['alt'] ?? $product->name }}" class="w-100 h-auto" width="540" height="720"
                        style="aspect-ratio: 3 / 4; object-fit: cover;">
                </a>
                @if ($galleryImages->count() > 1)
                    <div class="d-flex flex-wrap gap-3 mt-6">
                        @foreach ($galleryImages as $galleryImage)
                            <button type="button" class="border rounded bg-transparent p-1"
                                data-wholesale-image="{{ asset($galleryImage['image']) }}"
                                data-image-alt="{{ $galleryImage['alt'] }}" aria-label="View {{ $galleryImage['alt'] }}">
                                <img src="{{ asset($galleryImage['image']) }}" alt="{{ $galleryImage['alt'] }}"
                                    width="75" height="100" loading="lazy" style="object-fit: cover;">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-md-6 pt-md-0 pt-10">
                @if ($product->category)
                    <p class="fs-13px text-uppercase fw-semibold text-muted mb-4">
                        {{ $product->category->name }}
                        @if ($product->subcategory)
                            / {{ $product->subcategory->name }}
                        @endif
                    </p>
                @endif
                <span class="badge bg-primary mb-4">Wholesale</span>
                <h1 class="mb-5 fs-4">{{ $product->name }}</h1>
                @if ($product->short_description)
                    <p class="fs-15px mb-6">{{ $product->short_description }}</p>
                @endif
                <div class="border rounded p-4 mb-6">
                    <p class="mb-2">Minimum wholesale quantity: <strong>{{ number_format($minimum) }} units</strong></p>
                    @if ($product->has_variants)
                        <p class="small text-muted mb-2">
                            {{ $mixed ? 'Combine eligible options to meet the minimum. Each option retains its own prices.' : 'Each selected option must meet the minimum independently.' }}
                        </p>
                    @endif
                    <p class="small text-muted mb-0">Prices are in Nigerian naira (NGN). Delivery is calculated at checkout.
                    </p>
                </div>

                <form id="wholesale-order-form" method="POST" action="{{ $orderUrl ?: url()->current() }}"
                    data-order-url="{{ $orderUrl }}" data-minimum="{{ $minimum }}"
                    data-mixed="{{ $mixed ? '1' : '0' }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    @foreach ($sources as $index => $source)
                        @php
                            $isVariant = (bool) $product->has_variants;
                            $sourceId = $isVariant ? (string) $source->id : 'simple';
                            $sourceName = $isVariant ? $source->name : $product->name;
                            $stock = $isVariant ? (int) $source->stock_quantity : (int) $source->quantity;
                            $available = $source->track_stock
                                ? $source->stock_status === 'in_stock' && $stock > 0
                                : $source->stock_status !== 'out_of_stock';
                            $initialQuantity = $isVariant
                                ? 0
                                : ($available && (!$source->track_stock || $stock >= $minimum)
                                    ? $minimum
                                    : 0);
                            $sourceImage = $isVariant && $source->image ? asset($source->image) : '';
                        @endphp
                        <div class="border rounded p-4 mb-4" data-order-row data-source-id="{{ $sourceId }}">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                @if ($sourceImage)
                                    <button type="button" class="border-0 bg-transparent p-0"
                                        data-wholesale-image="{{ $sourceImage }}" data-image-alt="{{ $sourceName }}"
                                        aria-label="View {{ $sourceName }} image">
                                        <img src="{{ $sourceImage }}" alt="{{ $sourceName }}" width="50"
                                            height="65" class="rounded" style="object-fit: cover;">
                                    </button>
                                @endif
                                <div>
                                    <h2 class="fs-6 mb-1">{{ $sourceName }}</h2>
                                    @if ($source->sku)
                                        <span class="small text-muted">SKU: {{ $source->sku }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">Quantity From</th>
                                            <th scope="col">Price Per Unit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr data-price-tier="{{ $minimum }}">
                                            <td>{{ number_format($minimum) }}</td>
                                            <td>₦{{ number_format((float) $source->wholesale_price_ngn, 2) }}</td>
                                        </tr>
                                        @foreach ($source->wholesalePriceTiers as $tier)
                                            <tr data-price-tier="{{ $tier->min_quantity }}">
                                                <td>{{ number_format($tier->min_quantity) }}</td>
                                                <td>₦{{ number_format((float) $tier->price_ngn, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="items[{{ $index }}][variant_id]"
                                value="{{ $isVariant ? $source->id : '' }}">
                            <label class="form-label" for="wholesale-quantity-{{ $sourceId }}">Quantity</label>
                            <input type="number" id="wholesale-quantity-{{ $sourceId }}"
                                name="items[{{ $index }}][quantity]" value="{{ $initialQuantity }}"
                                class="form-control" min="0" step="1" data-wholesale-quantity
                                data-variant-id="{{ $isVariant ? $source->id : '' }}" data-image="{{ $sourceImage }}"
                                data-image-alt="{{ $sourceName }}"
                                max="{{ $available ? ($source->track_stock ? $stock : 4294967295) : 0 }}"
                                @disabled(!$available)>
                            @if (!$available)
                                <p class="small text-muted mt-2 mb-0">Out of stock</p>
                            @elseif ($source->track_stock)
                                <p class="small text-muted mt-2 mb-0">{{ number_format($stock) }} units in stock</p>
                            @endif
                            <p class="small mt-3 mb-0">
                                Unit price: <strong
                                    data-unit-price>₦{{ number_format((float) $source->wholesale_price_ngn, 2) }}</strong>
                                · Line total: <strong data-line-total>₦0.00</strong>
                            </p>
                        </div>
                    @endforeach
                    <div class="border rounded p-4 mt-5" aria-live="polite" aria-atomic="true">
                        <div class="d-flex justify-content-between mb-2"><span>Total units</span><strong
                                id="wholesale-total-units">0</strong></div>
                        <div class="d-flex justify-content-between"><span>Product total</span><strong
                                id="wholesale-total-price">₦0.00</strong></div>
                        <p class="small text-muted mt-3 mb-0" id="wholesale-quantity-message">Choose your quantities.</p>
                    </div>
                    <div class="alert alert-danger mt-4 d-none" role="alert" id="wholesale-order-error"></div>
                    <button type="submit" id="wholesale-order-button" class="btn btn-dark w-100 mt-5" disabled>
                        {{ $orderingAvailable ? 'Add Wholesale Order to Cart' : 'Ordering Currently Unavailable' }}
                    </button>
                    <noscript>
                        <p class="small mt-3">Enable JavaScript to calculate and submit your wholesale order.</p>
                    </noscript>
                </form>
            </div>
        </div>
    </section>
    @if ($product->description)
        <section class="container pb-14">
            <h2 class="fs-4 mb-5">Product Description</h2>{!! nl2br(e($product->description)) !!}
        </section>
    @endif
    @if ($relatedProducts->isNotEmpty())
        <section class="container container-xxl pb-16">
            <h2 class="fs-4 mb-7">More Wholesale Products</h2>
            <div class="row g-4">
                @foreach ($relatedProducts as $related)
                    @php
                        $relatedImage = $related->main_image ?: $related->images->first()?->image;
                    @endphp
                    <div class="col-lg-3 col-sm-6">
                        <a href="{{ url('/wholesale/' . $related->slug) }}" class="text-decoration-none text-reset">
                            <img src="{{ asset($relatedImage ?: 'assets/images/products/product-placeholder.jpg') }}"
                                alt="{{ $related->name }}" class="w-100 rounded mb-3" width="330" height="440"
                                loading="lazy" style="aspect-ratio: 3 / 4; object-fit: cover;">
                            <h3 class="fs-6">{{ $related->name }}</h3>
                            <p class="small mb-1">
                                {{ $related->has_variants ? 'From ' : '' }}₦{{ number_format((float) $related->wholesale_from_price_ngn, 2) }}
                                per unit</p>
                            <p class="small text-muted">Minimum: {{ number_format($related->wholesale_min_quantity) }}
                                units</p>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('wholesale-order-form');
            if (!form) return;
            const prices = @json($priceData);
            const minimum = Number(form.dataset.minimum);
            const mixed = form.dataset.mixed === '1';
            const rows = Array.from(form.querySelectorAll('[data-order-row]'));
            const button = document.getElementById('wholesale-order-button');
            const errorBox = document.getElementById('wholesale-order-error');
            let busy = false;

            function kobo(price) {
                const [whole, fraction = ''] = String(price).split('.');
                return BigInt(whole || '0') * 100n + BigInt(fraction.padEnd(2, '0').slice(0, 2));
            }

            function money(amount) {
                return '₦' + (amount / 100n).toLocaleString('en-NG') + '.' + (amount % 100n).toString().padStart(2,
                    '0');
            }

            function quantity(input) {
                const value = Number(input.value);
                return Number.isInteger(value) && value >= 0 && value <= 4294967295 ? value : 0;
            }

            function showImage(url, alt) {
                if (!url) return;
                const image = document.getElementById('wholesale-main-image');
                image.src = url;
                image.alt = alt || '';
                document.getElementById('wholesale-main-image-link').href = url;
            }
            document.querySelectorAll('[data-wholesale-image]').forEach(trigger => {
                trigger.addEventListener('click', () => showImage(trigger.dataset.wholesaleImage, trigger
                    .dataset.imageAlt));
            });

            function calculate() {
                const selected = rows.map(row => {
                    const input = row.querySelector('[data-wholesale-quantity]');
                    return {
                        row,
                        input,
                        quantity: input.disabled ? 0 : quantity(input)
                    };
                });
                const totalUnits = selected.reduce((total, item) => total + item.quantity, 0);
                let total = 0n;
                let valid = totalUnits > 0;
                let message = 'Your quantities qualify for wholesale pricing.';

                selected.forEach(item => {
                    const source = prices.find(source => source.id === item.row.dataset.sourceId);
                    const qualifyingQuantity = mixed ? totalUnits : item.quantity;
                    const tiers = source.tiers.slice().sort((a, b) => a.minimum - b.minimum);
                    let unitPrice = kobo(source.base);
                    let appliedMinimum = minimum;
                    tiers.forEach(tier => {
                        if (qualifyingQuantity >= tier.minimum) {
                            unitPrice = kobo(tier.price);
                            appliedMinimum = tier.minimum;
                        }
                    });
                    item.row.querySelector('[data-unit-price]').textContent = money(unitPrice);
                    item.row.querySelector('[data-line-total]').textContent = money(unitPrice * BigInt(item
                        .quantity));
                    total += unitPrice * BigInt(item.quantity);
                    item.row.querySelectorAll('[data-price-tier]').forEach(tierRow => {
                        tierRow.classList.toggle('table-active', item.quantity > 0 &&
                            qualifyingQuantity >= minimum &&
                            Number(tierRow.dataset.priceTier) === appliedMinimum);
                    });
                    if (!item.input.disabled && !item.input.validity.valid) {
                        valid = false;
                        message = 'Enter whole quantities within the available stock.';
                    } else if (item.quantity > 0 && !mixed && item.quantity < minimum) {
                        valid = false;
                        message = `Each selected option requires at least ${minimum} units.`;
                    }
                });
                if (totalUnits === 0) {
                    valid = false;
                    message = 'Choose quantities for the items you want to purchase.';
                } else if (mixed && totalUnits < minimum) {
                    valid = false;
                    message = `Add ${minimum - totalUnits} more units to reach the ${minimum}-unit minimum.`;
                }
                document.getElementById('wholesale-total-units').textContent = totalUnits.toLocaleString('en-NG');
                document.getElementById('wholesale-total-price').textContent = money(total);
                document.getElementById('wholesale-quantity-message').textContent = message;
                button.disabled = busy || !valid || !form.dataset.orderUrl;
                return {
                    valid,
                    items: selected.filter(item => item.quantity > 0).map(item => ({
                        variant_id: item.input.dataset.variantId ? Number(item.input.dataset
                            .variantId) : null,
                        quantity: item.quantity
                    }))
                };
            }
            rows.forEach(row => {
                const input = row.querySelector('[data-wholesale-quantity]');
                input.addEventListener('input', calculate);
                input.addEventListener('focus', () => showImage(input.dataset.image, input.dataset
                    .imageAlt));
            });
            form.addEventListener('submit', async event => {
                event.preventDefault();
                const order = calculate();
                if (busy || !order.valid || !form.dataset.orderUrl || !form.reportValidity()) return;
                busy = true;
                calculate();
                button.textContent = 'Adding Order…';
                errorBox.classList.add('d-none');
                try {
                    const response = await fetch(form.dataset.orderUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value
                        },
                        body: JSON.stringify({
                            product_id: Number(form.querySelector('[name="product_id"]')
                                .value),
                            items: order.items
                        })
                    });
                    const data = await response.json();
                    if (!response.ok || !data.status) throw new Error(data.message || Object.values(data
                        .errors || {}).flat()[0] || 'Unable to add this wholesale order.');
                    window.location.reload();
                } catch (error) {
                    errorBox.textContent = error.message || 'Unable to add this wholesale order.';
                    errorBox.classList.remove('d-none');
                } finally {
                    busy = false;
                    button.textContent = 'Add Wholesale Order to Cart';
                    calculate();
                }
            });
            calculate();
        });
    </script>
@endpush
