@extends('layouts.app')

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'Login',
            'item' => 'Login',
        ])

        <div class="container">

            <div class="text-center pt-13 mb-12 mb-lg-15">
                <h2 class="fs-36px mb-3">My Account</h2>
                <p class="fs-15 text-body mb-0">
                    Sign in to manage your account and orders
                </p>
            </div>

            <div class="row no-gutters">
                <div class="col-lg-10 mx-auto">
                    <div class="row no-gutters">

                        {{-- Login --}}
                        <div class="col-lg-6 mb-15 mb-lg-0 pe-lg-6 pe-xl-12">

                            <h3 class="fs-4 mb-10">Log In</h3>

                            <form id="customerLoginForm" method="POST" action="{{ route('login') }}" data-parsley-validate>

                                @csrf

                                <div class="form-group mb-6">
                                    <label for="login_email" class="visually-hidden">
                                        Email Address
                                    </label>

                                    <input type="email" name="email" id="login_email" class="form-control"
                                        value="{{ old('email') }}" placeholder="Email Address" autocomplete="email"
                                        required autofocus>
                                </div>

                                <div class="form-group mb-6 position-relative">
                                    <label for="login_password" class="visually-hidden">
                                        Password
                                    </label>

                                    <input type="password" name="password" id="login_password" class="form-control pe-12"
                                        placeholder="Password" autocomplete="current-password" required>

                                    <button type="button" id="togglePassword"
                                        class="position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent px-4"
                                        aria-label="Show password">

                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                </div>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="d-inline-block fs-15 lh-12 mb-7">
                                        Forgot your password?
                                    </a>
                                @endif

                                <button type="submit" class="btn btn-primary w-100 mb-7" id="loginBtn">
                                    Log In
                                </button>

                                <div class="form-check mb-7 d-flex">
                                    <input type="checkbox" class="form-check-input rounded-0" id="remember" name="remember"
                                        value="1" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label fs-15 ps-4 text-body-emphasis" for="remember">
                                        Keep me signed in.
                                    </label>
                                </div>

                            </form>
                        </div>

                        {{-- New Customer --}}
                        <div class="col-lg-6 ps-lg-6 ps-xl-12">

                            <h3 class="fs-4 mb-8">New Customer</h3>

                            <p class="mb-8">
                                Create a Delamoure account for faster checkout,
                                saved delivery addresses, order tracking and easier
                                account management.
                            </p>

                            <ul class="list-unstyled mb-8">
                                <li class="mb-3">
                                    <i class="fa-solid fa-check me-3 text-primary"></i>
                                    Faster checkout
                                </li>

                                <li class="mb-3">
                                    <i class="fa-solid fa-check me-3 text-primary"></i>
                                    Save delivery addresses
                                </li>

                                <li class="mb-3">
                                    <i class="fa-solid fa-check me-3 text-primary"></i>
                                    View and track orders
                                </li>

                                <li class="mb-3">
                                    <i class="fa-solid fa-check me-3 text-primary"></i>
                                    Manage account details
                                </li>
                            </ul>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary">
                                    Create Account
                                </a>
                            @endif

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(function() {

            const notyf = typeof Notyf !== 'undefined' ?
                new Notyf({
                    duration: 3500,
                    position: {
                        x: 'right',
                        y: 'top'
                    },
                    dismissible: true,
                    ripple: true
                }) :
                null;

            $('#togglePassword').on('click', function() {
                const input = $('#login_password');
                const icon = $(this).find('i');
                const showPassword = input.attr('type') === 'password';

                input.attr('type', showPassword ? 'text' : 'password');
                icon.toggleClass('fa-eye fa-eye-slash');

                $(this).attr(
                    'aria-label',
                    showPassword ? 'Hide password' : 'Show password'
                );
            });

            $('#customerLoginForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const btn = $('#loginBtn');
                const originalContent = btn.html();

                if ($.fn.parsley && !form.parsley().validate()) {
                    return false;
                }

                btn.prop('disabled', true).html(
                    '<i class="fa-solid fa-spinner fa-spin me-2"></i>Logging in...'
                );

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },

                    success: function(response) {
                        if (notyf) {
                            notyf.success(response.message || 'Login successful.');
                        }

                        window.location.href =
                            response.redirect || "{{ route('home') }}";
                    },

                    error: function(xhr) {
                        const response = xhr.responseJSON || {};
                        let message = response.message ||
                            'Login failed. Please try again.';

                        if (xhr.status === 422 && response.errors) {
                            const errors = Object.values(response.errors);
                            message = errors.length ? errors[0][0] : message;
                        }

                        if (notyf) {
                            notyf.error(message);
                        } else {
                            alert(message);
                        }
                    },

                    complete: function() {
                        btn.prop('disabled', false).html(originalContent);
                    }
                });

                return false;
            });

        });
    </script>
@endpush
