@extends('layouts.admin')

@section('content')
    <div class="body d-flex py-3">
        <div class="container-xxl">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Edit Variant</h3>
                    <div class="text-muted">
                        {{ $product->name }} / {{ $variant->name }}
                    </div>
                </div>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-secondary">Back to
                    Variants</a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.products.variants.partials.fields', [
                            'prefix' => 'variant-' . $variant->id,
                        ])
                        <button type="submit" class="btn btn-primary mt-4">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
