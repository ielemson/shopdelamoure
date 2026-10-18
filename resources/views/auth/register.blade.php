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
                            <label for="first_name" class="visually-hidden">
                                First Name
                            </label>

                            <input type="text" name="first_name" id="first_name" class="form-control"
                                value="{{ old('first_name') }}" placeholder="First Name" autocomplete="given-name" required>
                        </div>

                        <div class="col-md-6 mb-6">
                            <label for="last_name" class="visually-hidden">
                                Last Name
                            </label>

                            <input type="text" name="last_name" id="last_name" class="form-control"
                                value="{{ old('last_name') }}" placeholder="Last Name" autocomplete="family-name" required>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="email" class="visually-hidden">
                            Email Address
                        </label>

                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"
                            placeholder="Email Address" autocomplete="email" required>
                    </div>

                    <div class="mb-6">
                        <label for="phone" class="visually-hidden">
                            Phone Number
                        </label>

                        <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}"
                            placeholder="Phone Number" autocomplete="tel" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <div class="mb-6 position-relative">
                                <label for="password" class="visually-hidden">
                                    Password
                                </label>

                                <input type="password" name="password" id="password" class="form-control pe-12"
                                    placeholder="Password" autocomplete="new-password" data-parsley-minlength="8" required>

                                <button type="button"
                                    class="password-toggle position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent px-4"
                                    data-target="#password" aria-label="Show password" aria-pressed="false">
                                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
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
                                    data-target="#password_confirmation" aria-label="Show password" aria-pressed="false">
                                    <i class="fa-regular fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Retained pending the registration controller update. --}}
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
                            id="agree_terms" required
                            data-parsley-required-message="Please accept the terms to continue.">

                        <label class="form-check-label text-secondary" for="agree_terms">
                            I agree to Delamoure's
                            <a href="#" class="text-decoration-underline">
                                Privacy Policy
                            </a>
                            and
                            <a href="#" class="text-decoration-underline">
                                Terms of Use
                            </a>.
                        </label>
                    </div>

                    {{-- Cloudflare Turnstile --}}
                    <div class="mb-7">
                        <div id="register-turnstile" class="cf-turnstile"
                            data-sitekey="{{ config('services.turnstile.site_key') }}" data-action="register"
                            data-theme="light" data-size="flexible">
                        </div>
                    </div>

                    <div id="registerFeedback" class="alert alert-danger d-none" role="alert" aria-live="polite">
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
    {{-- Include the Turnstile API only once per page. --}}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <script>
        $(function() {
            const form = $('#customerRegisterForm');
            const btn = $('#registerBtn');
            const feedback = $('#registerFeedback');
            const originalText = btn.html();

            let submitting = false;
            let registered = false;

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

            function showError(message) {
                feedback.text(message).removeClass('d-none');

                if (notyf) {
                    notyf.error(message);
                }
            }

            form.find('.password-toggle').on('click', function() {
                const toggle = $(this);
                const input = $(toggle.data('target'));
                const icon = toggle.find('i');
                const show = input.attr('type') === 'password';

                input.attr('type', show ? 'text' : 'password');

                icon
                    .toggleClass('fa-eye', !show)
                    .toggleClass('fa-eye-slash', show);

                toggle
                    .attr('aria-label', show ? 'Hide password' : 'Show password')
                    .attr('aria-pressed', String(show));
            });

            form.on('submit', function(e) {
                e.preventDefault();

                if (submitting || registered) {
                    return;
                }

                feedback.addClass('d-none').empty();

                if ($.fn.parsley) {
                    if (!form.parsley().validate()) {
                        return;
                    }
                } else if (!form[0].reportValidity()) {
                    return;
                }

                const token = form
                    .find('[name="cf-turnstile-response"]')
                    .val();

                if (!token) {
                    showError(
                        'Please complete the security verification before registering.'
                    );

                    return;
                }

                submitting = true;

                btn
                    .prop('disabled', true)
                    .html(
                        '<i class="fa-solid fa-spinner fa-spin me-2" ' +
                        'aria-hidden="true"></i>Creating Account...'
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
                        registered = true;

                        if (notyf) {
                            notyf.success(
                                response.message ||
                                'Account created successfully.'
                            );
                        }

                        btn.text('Account Created');

                        window.location.href =
                            response.redirect || @json(route('home'));
                    },

                    error: function(xhr) {
                        const response = xhr.responseJSON || {};

                        if (xhr.status === 422 && response.errors) {
                            const messages = Object
                                .values(response.errors)
                                .flat();

                            showError(
                                messages.join(' ') ||
                                'Please check your details and try again.'
                            );

                            return;
                        }

                        let message =
                            response.message ||
                            'Registration failed. Please try again.';

                        if (xhr.status === 419) {
                            message =
                                'Your session has expired. Refresh the page and try again.';
                        } else if (xhr.status === 429) {
                            message =
                                'Too many attempts. Please wait before trying again.';
                        }

                        showError(message);
                    },

                    complete: function() {
                        if (!registered) {
                            if (window.turnstile) {
                                window.turnstile.reset('#register-turnstile');
                            }

                            submitting = false;

                            btn
                                .prop('disabled', false)
                                .html(originalText);
                        }
                    }
                });
            });
        });
    </script>
@endpush
