@extends('layouts.app')

@section('meta_title', 'Contact Us | Dela Moure Luxury Fragrances & Home Scents')
@section('meta_description',
    'Contact Dela Moure for enquiries, product assistance, orders, delivery support and help
    with our luxury perfumes, diffusers, candles, essential oils and gift sets.')

@section('PageContent')
    <section class="pb-lg-20 pb-16">

        @include('frontend.partials.breadcrumb', [
            'title' => 'Contact Us',
            'item' => 'Contact Us',
        ])

        <section class="py-15 py-lg-18">
            <div class="container">
                <div class="row">

                    {{-- Contact Form --}}
                    <div class="col-lg-7">

                        <div class="mb-10">
                            <p class="text-uppercase fw-semibold fs-15px mb-3">
                                Get in Touch
                            </p>

                            <h2 class="fs-3 mb-4">
                                We'd Love to Hear From You
                            </h2>

                            <p class="fs-6 text-body mb-0">
                                Have a question about an order, a product, or simply need
                                some assistance? Send us a message and the
                                {{ $setting?->website_name ?? 'Delamoure' }} team will be
                                happy to help.
                            </p>
                        </div>

                        <form id="contactForm" class="contact-form" method="POST" action="{{ route('contact.send') }}"
                            data-parsley-validate novalidate>
                            @csrf

                            <div class="row mb-8 mb-md-10">
                                <div class="col-md-6 col-12 mb-8 mb-md-0">
                                    <label for="contact-name" class="mb-3 fw-semibold text-body-emphasis">
                                        Name
                                    </label>

                                    <input type="text" name="name" id="contact-name" class="form-control input-focus"
                                        placeholder="Your name" required
                                        data-parsley-required-message="Please enter your name." data-parsley-minlength="2">
                                </div>

                                <div class="col-md-6 col-12">
                                    <label for="contact-email" class="mb-3 fw-semibold text-body-emphasis">
                                        Email Address
                                    </label>

                                    <input type="email" name="email" id="contact-email" class="form-control input-focus"
                                        placeholder="you@example.com" required data-parsley-type="email"
                                        data-parsley-required-message="Please enter your email address."
                                        data-parsley-type-message="Please enter a valid email address.">
                                </div>
                            </div>

                            <div class="mb-8">
                                <label for="contact-subject" class="mb-3 fw-semibold text-body-emphasis">
                                    Subject
                                </label>

                                <input type="text" name="subject" id="contact-subject" class="form-control input-focus"
                                    placeholder="How can we help?" required
                                    data-parsley-required-message="Please enter a subject." data-parsley-minlength="3">
                            </div>

                            <div class="mb-8">
                                <label for="contact-message" class="mb-3 fw-semibold text-body-emphasis">
                                    Message
                                </label>

                                <textarea name="message" id="contact-message" class="form-control input-focus" placeholder="Tell us how we can help..."
                                    rows="7" required data-parsley-required-message="Please enter your message." data-parsley-minlength="10"
                                    data-parsley-minlength-message="Your message should contain at least 10 characters."></textarea>
                            </div>

                            <button type="submit" id="contactSubmitBtn"
                                class="btn btn-dark btn-hover-bg-primary btn-hover-border-primary px-11">
                                Send Message
                            </button>
                        </form>

                    </div>


                    {{-- Contact Information --}}
                    <div class="col-lg-5 ps-lg-18 ps-xl-21 mt-13 mt-lg-0">

                        {{-- Customer Care --}}
                        <div class="mb-11">
                            <h3 class="fs-5 mb-5">
                                {{ $setting?->support_message_title ?: 'Customer Care' }}
                            </h3>

                            <p class="fs-6 mb-5">
                                {{ $setting?->support_message_body ?: 'Whether you need help choosing a product, tracking an order, or learning more about our products, our customer care team is here for you.' }}
                            </p>

                            @if (!empty($setting?->support_name))
                                <div class="d-flex align-items-center mb-6">

                                    @if (!empty($setting?->support_image))
                                        <img src="{{ asset('storage/' . $setting->support_image) }}"
                                            alt="{{ $setting->support_name }}" class="rounded-circle me-4" width="55"
                                            height="55" style="object-fit: cover;">
                                    @endif

                                    <div>
                                        <h6 class="mb-1">
                                            {{ $setting->support_name }}
                                        </h6>

                                        @if (!empty($setting?->support_role))
                                            <p class="mb-0 fs-15px text-body">
                                                {{ $setting->support_role }}
                                            </p>
                                        @endif
                                    </div>

                                </div>
                            @endif

                            @if (!empty($setting?->email))
                                <a href="mailto:{{ $setting->email }}" class="text-decoration-none text-body-emphasis">
                                    {{ $setting->email }}
                                </a>
                            @endif
                        </div>


                        {{-- Contact Details --}}
                        <div class="mb-11">
                            <h3 class="fs-5 mb-5">
                                Contact Information
                            </h3>

                            <ul class="list-unstyled mb-0">

                                @if (!empty($setting?->address))
                                    <li class="d-flex mb-5">
                                        <i class="fas fa-map-marker-alt me-4 mt-1" aria-hidden="true"></i>

                                        <div>
                                            <span class="fw-semibold d-block mb-1">
                                                Address
                                            </span>

                                            <span class="text-body">
                                                {{ $setting->address }}
                                            </span>
                                        </div>
                                    </li>
                                @endif


                                @if (!empty($setting?->phone))
                                    <li class="d-flex mb-5">
                                        <i class="fas fa-phone-alt me-4 mt-1" aria-hidden="true"></i>

                                        <div>
                                            <span class="fw-semibold d-block mb-1">
                                                Call Us
                                            </span>

                                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $setting->phone) }}"
                                                class="text-decoration-none text-body">
                                                {{ $setting->phone }}
                                            </a>
                                        </div>
                                    </li>
                                @endif


                                @if (!empty($setting?->email))
                                    <li class="d-flex">
                                        <i class="fas fa-envelope me-4 mt-1" aria-hidden="true"></i>

                                        <div>
                                            <span class="fw-semibold d-block mb-1">
                                                Email
                                            </span>

                                            <a href="mailto:{{ $setting->email }}" class="text-decoration-none text-body">
                                                {{ $setting->email }}
                                            </a>
                                        </div>
                                    </li>
                                @endif

                            </ul>
                        </div>


                        {{-- WhatsApp --}}
                        @if (!empty($setting?->support_phone) || !empty($setting?->phone))
                            @php
                                $whatsappNumber = $setting?->support_phone ?: $setting?->phone;
                                $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappNumber);

                                if (str_starts_with($whatsappNumber, '0')) {
                                    $whatsappNumber = '234' . substr($whatsappNumber, 1);
                                }
                            @endphp

                            <div class="mb-11">
                                <h3 class="fs-5 mb-5">
                                    Call or WhatsApp
                                </h3>

                                <p class="fs-6 mb-3">
                                    Speak directly with our customer care team.
                                </p>

                                <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener"
                                    class="text-decoration-none border-bottom border-currentColor fw-semibold fs-6">
                                    Contact Customer Care
                                </a>
                            </div>
                        @endif


                        {{-- Business Hours --}}
                        <div>
                            <h3 class="fs-5 mb-5">
                                Business Hours
                            </h3>

                            <div class="fs-6">
                                <p class="mb-3">
                                    Monday – Friday: 9:00 AM – 5:00 PM
                                </p>

                                <p class="mb-0">
                                    Saturday: 10:00 AM – 3:00 PM
                                </p>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </section>

    </section>
@endsection


@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
    <script>
        $(function() {
            const form = $('#contactForm');
            const parsley = form.parsley();

            form.on('submit', function(e) {
                e.preventDefault();

                if (!parsley.isValid()) {
                    parsley.validate();
                    return;
                }

                const button = $('#contactSubmitBtn');
                const originalText = button.html();

                button
                    .prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),

                    success: function(response) {
                        $.notify(
                            response.message ||
                            'Your message has been sent successfully.', {
                                className: 'success',
                                globalPosition: 'top right',
                                autoHideDelay: 4000
                            }
                        );

                        form[0].reset();
                        parsley.reset();
                    },

                    error: function(xhr) {
                        let message = 'Something went wrong. Please try again.';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat()[0];
                        } else if (xhr.responseJSON?.message) {
                            message = xhr.responseJSON.message;
                        }

                        $.notify(message, {
                            className: 'error',
                            globalPosition: 'top right',
                            autoHideDelay: 5000
                        });
                    },

                    complete: function() {
                        button
                            .prop('disabled', false)
                            .html(originalText);
                    }
                });
            });
        });
    </script>
@endpush
