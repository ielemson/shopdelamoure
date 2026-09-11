@extends('layouts.customer')

@section('CustomerContent')

    <div class="dashboard-page-content">

        {{-- ==========================================================
            PAGE HEADER
        ========================================================== --}}

        <div class="row mb-9 align-items-center justify-content-between">

            <div class="col-sm-8">

                <h2 class="fs-4 mb-2">
                    My Profile
                </h2>

                <p class="mb-0 text-muted">
                    Manage your personal information and account security.
                </p>

            </div>

        </div>


        {{-- ==========================================================
            SUCCESS MESSAGE
        ========================================================== --}}

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-7">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>
        @endif


        {{-- ==========================================================
            VALIDATION ERRORS
        ========================================================== --}}

        @if ($errors->any())
            <div class="alert alert-danger mb-7">

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>

            </div>
        @endif


        <div class="row g-6">


            {{-- ======================================================
                PERSONAL INFORMATION
            ====================================================== --}}

            <div class="col-lg-7">

                <div class="card rounded-4 p-7 h-100">

                    <div class="card-header bg-transparent border-0 px-0 pt-0 pb-7">

                        <h4 class="fs-18px mb-2">
                            Personal Information
                        </h4>

                        <p class="text-muted fs-14px mb-0">
                            Update your basic account details.
                        </p>

                    </div>


                    <div class="card-body px-0 py-0">

                        <form action="{{ route('customer.profile.update') }}" method="POST">

                            @csrf
                            @method('PATCH')


                            {{-- Name --}}
                            <div class="mb-6">

                                <label for="profileName" class="form-label fw-semibold">

                                    Full Name

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="text" id="profileName" name="name" class="form-control"
                                    value="{{ old('name', $user->name) }}" autocomplete="name" required>

                            </div>


                            {{-- Email --}}
                            <div class="mb-6">

                                <label for="profileEmail" class="form-label fw-semibold">

                                    Email Address

                                    <span class="text-danger">*</span>

                                </label>

                                <input type="email" id="profileEmail" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" autocomplete="email" required>


                                {{-- Verification Status --}}
                                <div class="mt-3">

                                    @if ($user->email_verified_at)
                                        <span class="badge bg-success">

                                            <i class="fa-solid fa-circle-check me-1"></i>

                                            Email Verified

                                        </span>
                                    @else
                                        <span class="badge bg-warning text-dark">

                                            <i class="fa-solid fa-circle-exclamation me-1"></i>

                                            Email Not Verified

                                        </span>
                                    @endif

                                </div>

                            </div>


                            {{-- Phone --}}
                            <div class="mb-7">

                                <label for="profilePhone" class="form-label fw-semibold">

                                    Phone Number

                                </label>

                                <input type="tel" id="profilePhone" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}" placeholder="e.g. 08012345678"
                                    autocomplete="tel">

                            </div>


                            {{-- Submit --}}
                            <button type="submit" class="btn btn-dark px-8 py-4">

                                <i class="fa-solid fa-floppy-disk me-2"></i>

                                Save Changes

                            </button>

                        </form>

                    </div>

                </div>

            </div>


            {{-- ======================================================
                SECURITY
            ====================================================== --}}

            <div class="col-lg-5">

                <div class="card rounded-4 p-7 h-100">

                    <div class="card-header bg-transparent border-0 px-0 pt-0 pb-7">

                        <h4 class="fs-18px mb-2">
                            Account Security
                        </h4>

                        <p class="text-muted fs-14px mb-0">
                            Change your account password securely.
                        </p>

                    </div>


                    <div class="card-body px-0 py-0">

                        <form action="{{ route('customer.profile.password') }}" method="POST">

                            @csrf
                            @method('PATCH')


                            {{-- Current Password --}}
                            <div class="mb-6">

                                <label for="currentPassword" class="form-label fw-semibold">

                                    Current Password

                                    <span class="text-danger">*</span>

                                </label>

                                <div class="position-relative">

                                    <input type="password" id="currentPassword" name="current_password"
                                        class="form-control pe-10" autocomplete="current-password" required>

                                    <button type="button"
                                        class="btn border-0 bg-transparent position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                        data-target="currentPassword" aria-label="Show password">

                                        <i class="fa-regular fa-eye"></i>

                                    </button>

                                </div>

                            </div>


                            {{-- New Password --}}
                            <div class="mb-6">

                                <label for="newPassword" class="form-label fw-semibold">

                                    New Password

                                    <span class="text-danger">*</span>

                                </label>

                                <div class="position-relative">

                                    <input type="password" id="newPassword" name="password" class="form-control pe-10"
                                        autocomplete="new-password" required>

                                    <button type="button"
                                        class="btn border-0 bg-transparent position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                        data-target="newPassword" aria-label="Show password">

                                        <i class="fa-regular fa-eye"></i>

                                    </button>

                                </div>

                                <small class="text-muted d-block mt-2">

                                    Use at least 8 characters with letters and numbers.

                                </small>

                            </div>


                            {{-- Confirm Password --}}
                            <div class="mb-7">

                                <label for="passwordConfirmation" class="form-label fw-semibold">

                                    Confirm New Password

                                    <span class="text-danger">*</span>

                                </label>

                                <div class="position-relative">

                                    <input type="password" id="passwordConfirmation" name="password_confirmation"
                                        class="form-control pe-10" autocomplete="new-password" required>

                                    <button type="button"
                                        class="btn border-0 bg-transparent position-absolute top-50 end-0 translate-middle-y me-2 password-toggle"
                                        data-target="passwordConfirmation" aria-label="Show password">

                                        <i class="fa-regular fa-eye"></i>

                                    </button>

                                </div>

                            </div>


                            {{-- Submit --}}
                            <button type="submit" class="btn btn-primary px-8 py-4">

                                <i class="fa-solid fa-lock me-2"></i>

                                Change Password

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            document
                .querySelectorAll('.password-toggle')
                .forEach(function(button) {

                    button.addEventListener('click', function() {

                        const targetId =
                            this.dataset.target;

                        const input =
                            document.getElementById(targetId);

                        const icon =
                            this.querySelector('i');


                        if (!input) {
                            return;
                        }


                        if (input.type === 'password') {

                            input.type = 'text';

                            icon.classList.remove(
                                'fa-eye'
                            );

                            icon.classList.add(
                                'fa-eye-slash'
                            );

                            this.setAttribute(
                                'aria-label',
                                'Hide password'
                            );

                        } else {

                            input.type = 'password';

                            icon.classList.remove(
                                'fa-eye-slash'
                            );

                            icon.classList.add(
                                'fa-eye'
                            );

                            this.setAttribute(
                                'aria-label',
                                'Show password'
                            );

                        }

                    });

                });

        });
    </script>
@endpush
