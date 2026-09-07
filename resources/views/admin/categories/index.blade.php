@extends('layouts.admin')

@section('content')
<!-- Body: Category Management -->
<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <h3 class="fw-bold mb-0">Categories</h3>

                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary py-2 px-4">
                        <i class="icofont-plus-circle me-2"></i>Add Category
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                        <h6 class="mb-0 fw-bold">Category List</h6>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0 table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Category</th>
                                        <th>Parent</th>
                                        <th>Featured</th>
                                        <th>Status</th>
                                        <th>Sort</th>
                                        <th>Created</th>
                                        <th class="text-end">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($categories as $key => $category)
                                        <tr>
                                            <td>{{ $categories->firstItem() + $key }}</td>

                                            <td>
                                                @if($category->image)
                                                    <img src="{{ asset($category->image) }}"
                                                         alt="{{ $category->name }}"
                                                         class="rounded"
                                                         width="55"
                                                         height="55"
                                                         style="object-fit: cover;">
                                                @else
                                                    <div class="avatar lg rounded bg-light d-flex align-items-center justify-content-center">
                                                        <i class="icofont-image text-muted fs-4"></i>
                                                    </div>
                                                @endif
                                            </td>

                                            <td>
                                                <strong>{{ $category->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $category->slug }}</small>
                                            </td>

                                            <td>
                                                {{ $category->parent?->name ?? 'Main Category' }}
                                            </td>

                                            <td>
                                                @if($category->is_featured)
                                                    <span class="badge bg-warning">Featured</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Normal</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($category->status)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>

                                            <td>{{ $category->sort_order }}</td>

                                            <td>{{ $category->created_at->format('d M, Y') }}</td>

                                            <td class="text-end">
                                                <a href="{{ route('admin.categories.edit', $category->id) }}"
                                                   class="btn btn-outline-secondary btn-sm">
                                                    <i class="icofont-edit text-success"></i>
                                                </a>

                                                <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                        <i class="icofont-ui-delete text-danger"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <h6 class="text-muted mb-2">No category found</h6>
                                                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
                                                    Add First Category
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection