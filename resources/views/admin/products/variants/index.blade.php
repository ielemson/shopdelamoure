@extends('layouts.admin')

@section('content')
    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Manage Variants</h3>
                    <div class="text-muted">{{ $product->name }}</div>
                </div>
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary me-2">Back to Products</a>
                    <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-primary">
                        <i class="icofont-plus me-1"></i>Add Variant
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-header py-3 bg-transparent">
                    <h6 class="fw-bold mb-0">Variants ({{ $variants->count() }})</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Image</th>
                                    <th>Variant</th>
                                    <th>Options</th>
                                    <th>Price (NGN)</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                    <th>Order</th>
                                    <th class="text-end pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($variants as $variant)
                                    <tr>
                                        <td class="ps-3">
                                            @if ($variant->image)
                                                <img src="{{ asset($variant->image) }}" alt="{{ $variant->name }}"
                                                    width="50" height="50" class="rounded"
                                                    style="object-fit: cover;">
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $variant->name }}</strong>
                                            @if ($variant->is_default)
                                                <span class="badge bg-primary ms-1">Default</span>
                                            @endif
                                            <div class="small text-muted">
                                                SKU: {{ $variant->sku ?: '—' }}
                                            </div>
                                        </td>
                                        <td>
                                            @forelse ($variant->options ?? [] as $key => $value)
                                                <div class="small">
                                                    <span class="text-muted">{{ ucfirst($key) }}:</span>
                                                    {{ $value }}
                                                </div>
                                            @empty
                                                <span class="text-muted">—</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @php
                                                $regular = $variant->effective_price_ngn;
                                                $sale = $variant->effective_sale_price_ngn;
                                                $hasSale =
                                                    $sale !== null &&
                                                    $regular !== null &&
                                                    (float) $sale < (float) $regular &&
                                                    $product->saleIsCurrentlyActive();
                                            @endphp

                                            @if ($hasSale)
                                                <strong class="text-success">
                                                    ₦{{ number_format((float) $sale, 2) }}
                                                </strong>
                                                <div class="small text-muted text-decoration-line-through">
                                                    ₦{{ number_format((float) $regular, 2) }}
                                                </div>
                                            @elseif ($regular !== null)
                                                ₦{{ number_format((float) $regular, 2) }}
                                            @else
                                                <span class="text-muted">No price</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($variant->track_stock)
                                                <strong>{{ $variant->stock_quantity }}</strong> units
                                                @if ($variant->isLowStock())
                                                    <div class="small text-warning">Low stock</div>
                                                @endif
                                            @else
                                                <span class="text-muted">Not tracked</span>
                                            @endif
                                            <div class="small">
                                                {{ $variant->isInStock() ? 'In stock' : 'Out of stock' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $variant->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $variant->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $variant->sort_order }}</td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.products.variants.edit', [$product, $variant]) }}"
                                                class="btn btn-outline-secondary btn-sm" title="Edit Variant">
                                                <i class="icofont-edit"></i>
                                            </a>

                                            <form
                                                action="{{ route('admin.products.variants.destroy', [$product, $variant]) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Delete this variant?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm"
                                                    title="Delete Variant">
                                                    <i class="icofont-ui-delete"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            No variants yet.
                                            <a href="{{ route('admin.products.variants.create', $product) }}">
                                                Add the first variant
                                            </a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
