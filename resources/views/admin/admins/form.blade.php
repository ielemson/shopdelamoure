@extends('layouts.admin')

@section('content')
    <div class="container-xxl py-4">
        <h3 class="mb-4">
            {{ $admin ? 'Edit Administrator' : 'Add Administrator' }}
        </h3>

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
                <form method="POST"
                    action="{{ $admin ? route('admin.admins.update', $admin) : route('admin.admins.store') }}">
                    @csrf

                    @if ($admin)
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" name="name" type="text" class="form-control"
                            value="{{ old('name', $admin?->name) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control"
                            value="{{ old('email', $admin?->email) }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            {{ $admin ? 'New Password' : 'Password' }}
                        </label>
                        <input id="password" name="password" type="password" class="form-control"
                            autocomplete="new-password" minlength="12" @required(!$admin)>

                        <div class="form-text">
                            At least 12 characters.
                            @if ($admin)
                                Leave blank to keep the current password.
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            Confirm Password
                        </label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control"
                            autocomplete="new-password" minlength="12" @required(!$admin)>
                    </div>

                    <fieldset class="mb-4">
                        <legend class="fs-6 fw-semibold">Assign Roles</legend>

                        @forelse ($roles as $role)
                            <div class="form-check mb-2">
                                <input id="role-{{ $role->id }}" name="roles[]" type="checkbox"
                                    value="{{ $role->name }}" class="form-check-input" @checked(in_array($role->name, old('roles', $selectedRoles), true))>

                                <label for="role-{{ $role->id }}" class="form-check-label">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @empty
                            <div class="alert alert-info">
                                Create a role with website settings or delivery
                                settings permissions in Role Management first.
                            </div>
                        @endforelse
                    </fieldset>

                    <button type="submit" class="btn btn-primary" @disabled($roles->isEmpty())>
                        {{ $admin ? 'Save Changes' : 'Create Administrator' }}
                    </button>

                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>
@endsection
