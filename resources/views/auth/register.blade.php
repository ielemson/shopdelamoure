@extends('layouts.app')

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'Register',
            'item' => 'Register',
        ])

        <div class="container">

            <div class="text-center pt-13 mb-12 mb-lg-15">
                <h2 class="fs-36px mb-3">Create Account</h2>
                <p class="fs-15 text-body mb-0">
                    Join Delamoure for a faster and more personalised shopping experience
                </p>
            </div>

            <div class="col-lg-6 col-md-8 mx-auto">

                <form id="customerRegisterForm" action="{{ route('register') }}" method="POST" data-parsley-validate>

                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <label for="first_name" class="visually-hidden">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control"
                                value="{{ old('first_name') }}" placeholder="First Name" required>
                        </div>

                        <div class="col-md-6 mb-6">
                            <label for="last_name" class="visually-hidden">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control"
                                value="{{ old('last_name') }}" placeholder="Last Name" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="email" class="visually-hidden">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                            placeholder="Email Address" autocomplete="email" required>
                    </div>

                    <div class="mb-6">
                        <label for="phone" class="visually-hidden">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}"
                            placeholder="Phone Number" autocomplete="tel" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <div class="mb-6 position-relative">
                                <label for="password" class="visually-hidden">Password</label>

                                <input type="password" name="password" id="password" class="form-control pe-12"
                                    placeholder="Password" autocomplete="new-password" data-parsley-minlength="8" required>

                                <button type="button"
                                    class="password-toggle position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent px-4"
                                    data-target="#password" aria-label="Show password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-6">
                            <div class="mb-6 position-relative">
                                <label for="password_confirmation" class="visually-hidden">
                                    Confirm Password
                                </label>

                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control pe-12" placeholder="Confirm Password" autocomplete="new-password"
                                    data-parsley-equalto="#password" data-parsley-equalto-message="Passwords do not match."
                                    required>

                                <button type="button"
                                    class="password-toggle position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent px-4"
                                    data-target="#password_confirmation" aria-label="Show password">
                                    <i class="fa-regular fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>




                    @if (session('captcha_question'))
                        <div class="mb-6">
                            <label for="captcha" class="form-label fs-15">
                                Security Check: {{ session('captcha_question') }}
                            </label>

                            <input type="number" name="captcha" id="captcha" class="form-control"
                                placeholder="Enter your answer" required>
                        </div>
                    @endif

                    <div class="form-check mb-7">
                        <input type="checkbox" name="agree" value="1" class="form-check-input rounded-0"
                            id="agree_terms" required data-parsley-required-message="Please accept the terms to continue.">

                        <label class="form-check-label text-secondary" for="agree_terms">
                            I agree to Delamoure's
                            <a href="#" class="text-decoration-underline">Privacy Policy</a>
                            and
                            <a href="#" class="text-decoration-underline">Terms of Use</a>.
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" id="registerBtn">
                        Create Account
                    </button>

                    <div class="text-center mt-7 fs-15">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-decoration-underline fw-semibold">
                            Log In
                        </a>
                    </div>

                </form>
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

            $('.password-toggle').on('click', function() {
                const btn = $(this);
                const input = $(btn.data('target'));
                const icon = btn.find('i');
                const show = input.attr('type') === 'password';

                input.attr('type', show ? 'text' : 'password');
                icon.toggleClass('fa-eye fa-eye-slash');
                btn.attr('aria-label', show ? 'Hide password' : 'Show password');
            });

            $('#customerRegisterForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const btn = $('#registerBtn');
                const originalText = btn.html();

                if ($.fn.parsley && !form.parsley().validate()) return false;

                btn.prop('disabled', true)
                    .html('<i class="fa-solid fa-spinner fa-spin me-2"></i>Creating Account...');

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
                            notyf.success(
                                response.message || 'Account created successfully.'
                            );
                        }

                        window.location.href =
                            response.redirect || "{{ route('home') }}";
                    },

                    error: function(xhr) {
                        const response = xhr.responseJSON || {};

                        if (xhr.status === 422 && response.errors) {
                            $.each(response.errors, function(key, errors) {
                                (Array.isArray(errors) ? errors : [errors])
                                .forEach(function(message) {
                                    if (notyf) notyf.error(message);
                                });
                            });

                            return;
                        }

                        const message =
                            response.message ||
                            'Registration failed. Please try again.';

                        if (notyf) {
                            notyf.error(message);
                        } else {
                            alert(message);
                        }
                    },

                    complete: function() {
                        btn.prop('disabled', false).html(originalText);
                    }
                });

                return false;
            });

        });
    </script>
@endpush
