@extends('layouts.admin')

@section('content')
<!-- Body: Edit Category -->
<div class="body d-flex py-3">
    <div class="container-xxl">

        <div class="row align-items-center">
            <div class="border-0 mb-4">
                <div class="card-header py-3 no-bg bg-transparent d-flex align-items-center px-0 justify-content-between border-bottom flex-wrap">
                    <h3 class="fw-bold mb-0">Edit Category</h3>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary py-2 px-4">
                        <i class="icofont-arrow-left me-2"></i>Back
                    </a>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3 mb-3">

                <div class="col-xl-8 col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Category Information</h6>
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="name"
                                       value="{{ old('name', $category->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="Enter category name">

                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Parent Category</label>
                                <select name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">Main Category</option>

                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}"
                                            @selected(old('parent_id', $category->parent_id) == $parent->id)>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                          rows="5"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Write short category description">{{ old('description', $category->description) }}</textarea>

                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4">

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Category Image</h6>
                        </div>

                        <div class="card-body">

                            @if($category->image)
                                <div class="mb-3">
                                    <img src="{{ asset($category->image) }}"
                                         alt="{{ $category->name }}"
                                         class="rounded border"
                                         width="100%"
                                         style="max-height: 220px; object-fit: cover;">
                                </div>
                            @endif

                            <div class="mb-3">
                                <input type="file"
                                       name="image"
                                       class="form-control @error('image') is-invalid @enderror"
                                       accept="image/*">

                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                <small class="text-muted d-block mt-2">
                                    Upload new image only if you want to replace the current one.
                                </small>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header py-3 bg-transparent border-bottom-0">
                            <h6 class="mb-0 fw-bold">Category Settings</h6>
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number"
                                       name="sort_order"
                                       value="{{ old('sort_order', $category->sort_order) }}"
                                       class="form-control @error('sort_order') is-invalid @enderror">

                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="is_featured"
                                       id="is_featured"
                                       value="1"
                                       @checked(old('is_featured', $category->is_featured))>

                                <label class="form-check-label" for="is_featured">
                                    Featured Category
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="status"
                                       id="status"
                                       value="1"
                                       @checked(old('status', $category->status))>

                                <label class="form-check-label" for="status">
                                    Active Category
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="icofont-save me-2"></i>Update Category
                            </button>

                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>
</div>
@endsection