@extends('layouts.admin')

@section('content')

    <div class="body d-flex py-3">
        <div class="container-xxl">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Form Error:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.website.settings.update') }}" method="POST" enctype="multipart/form-data">

                @csrf
                @method('PUT')

                <div class="row align-items-center mb-4">
                    <div class="col">
                        <h3 class="fw-bold mb-0">Website Settings</h3>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">
                            <i class="icofont-save me-2"></i>
                            Save Changes
                        </button>
                    </div>
                </div>

                <div class="row g-3">

                    {{-- LEFT SIDE --}}
                    <div class="col-lg-8">

                        {{-- WEBSITE INFORMATION --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Website Information</h6>
                            </div>

                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Website Name</label>
                                    <input type="text" name="website_name" class="form-control"
                                        value="{{ old('website_name', $setting->website_name) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', $setting->phone) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $setting->email) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" rows="4" class="form-control">{{ old('address', $setting->address) }}</textarea>
                                </div>

                            </div>
                        </div>

                        {{-- SUPPORT CONTACT WIDGET --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Support Contact Widget</h6>
                            </div>

                            <div class="card-body">

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Support Name</label>
                                        <input type="text" name="support_name" class="form-control"
                                            value="{{ old('support_name', $setting->support_name) }}"
                                            placeholder="Dela Moure Support">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Support Position</label>
                                        <input type="text" name="support_role" class="form-control"
                                            value="{{ old('support_role', $setting->support_role) }}"
                                            placeholder="Customer Care Representative">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Support Phone</label>
                                        <input type="text" name="support_phone" class="form-control"
                                            value="{{ old('support_phone', $setting->support_phone) }}"
                                            placeholder="+2348000000000">
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Greeting Title</label>
                                        <input type="text" name="support_message_title" class="form-control"
                                            value="{{ old('support_message_title', $setting->support_message_title) }}"
                                            placeholder="Hello 👋">
                                    </div>

                                    <div class="col-md-12 mb-0">
                                        <label class="form-label">Greeting Message</label>
                                        <textarea name="support_message_body" rows="4" class="form-control"
                                            placeholder="Thank you for visiting Dela Moure. How can we help you today?">{{ old('support_message_body', $setting->support_message_body) }}</textarea>
                                    </div>

                                </div>

                            </div>
                        </div>

                        {{-- SEO SETTINGS --}}
                        {{-- <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">SEO & Meta Information</h6>
                            </div>

                            <div class="card-body">

                                <div class="mb-3">
                                    <label class="form-label">Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control"
                                        value="{{ old('meta_title', $setting->meta_title) }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea name="meta_description" rows="4" class="form-control">{{ old('meta_description', $setting->meta_description) }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <textarea name="meta_keywords" rows="3" class="form-control">{{ old('meta_keywords', $setting->meta_keywords) }}</textarea>
                                </div>

                            </div>
                        </div> --}}

                        {{-- SOCIAL MEDIA --}}

                        <div class="card">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Social Media Links</h6>
                            </div>

                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="facebook" class="form-label">Facebook</label>
                                    <input type="url" id="facebook" name="facebook" class="form-control"
                                        value="{{ old('facebook', $setting->facebook) }}"
                                        placeholder="https://www.facebook.com/yourpage">
                                </div>

                                <div class="mb-3">
                                    <label for="instagram" class="form-label">Instagram</label>
                                    <input type="url" id="instagram" name="instagram" class="form-control"
                                        value="{{ old('instagram', $setting->instagram) }}"
                                        placeholder="https://www.instagram.com/yourusername">
                                </div>

                                <div class="mb-3">
                                    <label for="snapchat" class="form-label">Snapchat</label>
                                    <input type="url" id="snapchat" name="snapchat" class="form-control"
                                        value="{{ old('snapchat', $setting->snapchat) }}"
                                        placeholder="https://www.snapchat.com/add/yourusername">
                                </div>

                                <div class="mb-0">
                                    <label for="tiktok" class="form-label">TikTok</label>
                                    <input type="url" id="tiktok" name="tiktok" class="form-control"
                                        value="{{ old('tiktok', $setting->tiktok) }}"
                                        placeholder="https://www.tiktok.com/@yourusername">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="col-lg-4">

                        {{-- LOGO --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Website Logo</h6>
                            </div>

                            <div class="card-body">

                                @if ($setting->logo)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $setting->logo) }}"
                                            class="img-fluid rounded border" style="max-height:120px;">
                                    </div>
                                @endif

                                <input type="file" name="logo" class="form-control" accept="image/*">
                            </div>
                        </div>

                        {{-- FAVICON --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Favicon</h6>
                            </div>

                            <div class="card-body">

                                @if ($setting->favicon)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $setting->favicon) }}"
                                            class="img-fluid rounded border" style="max-height:80px;">
                                    </div>
                                @endif

                                <input type="file" name="favicon" class="form-control" accept="image/*">
                            </div>
                        </div>

                        {{-- SUPPORT IMAGE --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Support Profile Image</h6>
                            </div>

                            <div class="card-body">

                                @if ($setting->support_image)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $setting->support_image) }}"
                                            class="img-fluid rounded-circle border"
                                            style="width:120px;height:120px;object-fit:cover;">
                                    </div>
                                @endif

                                <input type="file" name="support_image" class="form-control" accept="image/*">

                                <small class="text-muted">
                                    Recommended size: 300 × 300px
                                </small>

                            </div>
                        </div>

                        {{-- ADMIN PASSWORD --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Update Admin Password</h6>
                            </div>

                            <div class="card-body">
                                <p class="text-muted small">
                                    Leave these fields blank to keep your current password.
                                </p>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">
                                        Current Password
                                    </label>
                                    <input type="password" id="current_password" name="current_password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        autocomplete="current-password">

                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        New Password
                                    </label>
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        autocomplete="new-password" minlength="8">

                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-0">
                                    <label for="password_confirmation" class="form-label">
                                        Confirm New Password
                                    </label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control" autocomplete="new-password" minlength="8">
                                </div>
                            </div>
                        </div>

                        {{-- CHECKOUT SETTINGS --}}
                        <div class="card mb-3">
                            <div class="card-header py-3">
                                <h6 class="mb-0 fw-bold">Checkout Settings</h6>
                            </div>

                            <div class="card-body">

                                {{-- Hidden value ensures 0 is submitted when unchecked --}}
                                <input type="hidden" name="enable_store_pickup" value="0">

                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input" id="enable_store_pickup"
                                        name="enable_store_pickup" value="1" @checked(old('enable_store_pickup', $setting->enable_store_pickup ?? true))>

                                    <label class="form-check-label fw-semibold" for="enable_store_pickup">
                                        Enable Store Pickup
                                    </label>
                                </div>

                                <div class="mt-2">
                                    <small class="text-muted">
                                        When disabled, customers will only be able
                                        to use home delivery during checkout.
                                    </small>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </form>

        </div>
    </div>

@endsection
