@extends('layouts.admin')

@section('content')
    <div class="body d-flex py-3">
        <div class="container-xxl">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0">Packaging Presets</h3>

                <a href="{{ route('admin.delivery.settings') }}" class="btn btn-outline-primary">
                    Delivery Settings
                </a>
            </div>

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

            <p class="mb-4">
                Save the actual boxes you use. Measure their outer dimensions
                and weigh each empty box with its usual dispatch padding.
                Product weights are recorded separately.
            </p>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="fw-bold mb-0">Add Packaging Preset</h6>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shipping-packages.store') }}">
                        @csrf

                        @include('admin.settings.packaging-fields', [
                            'packagePrefix' => 'new-package',
                            'packageItem' => null,
                        ])

                        <button type="submit" class="btn btn-primary mt-3">
                            Save Preset
                        </button>
                    </form>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Saved Packaging</h5>

            @forelse ($packages as $package)
                <details class="card mb-3">
                    <summary class="card-header">
                        {{ $package->name }}
                        — {{ $package->is_active ? 'Active' : 'Inactive' }}
                    </summary>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.shipping-packages.update', $package) }}">
                            @csrf
                            @method('PUT')

                            @include('admin.settings.packaging-fields', [
                                'packagePrefix' => 'package-' . $package->id,
                                'packageItem' => $package,
                            ])

                            <button type="submit" class="btn btn-primary mt-3">
                                Update Preset
                            </button>
                        </form>
                    </div>
                </details>
            @empty
                <div class="alert alert-info">
                    No packaging presets saved yet. Add your first box above.
                </div>
            @endforelse

        </div>
    </div>
@endsection
