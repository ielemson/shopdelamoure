@extends('layouts.admin')

@section('content')
    <div class="container-xxl py-4">
        <div class="mb-4">
            <h3 class="mb-1">Role Management</h3>
            <p class="text-muted mb-0">
                Create roles and choose which sections they can manage.
            </p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Create Role</h5>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="new-role-name" class="form-label">
                            Role Name
                        </label>

                        <input id="new-role-name" type="text" name="name" class="form-control"
                            placeholder="e.g. Delivery Manager" maxlength="100" required>
                    </div>

                    <label class="form-label d-block">Permissions</label>

                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input id="new-permission-{{ $permission->id }}" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}" class="form-check-input">

                                    <label for="new-permission-{{ $permission->id }}" class="form-check-label">
                                        {{ ucwords($permission->name) }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="small text-muted">
                        Manage Admins permits access to role management.
                        Grant this only to trusted administrators.
                    </p>

                    <button type="submit" class="btn btn-primary">
                        Create Role
                    </button>
                </form>
            </div>
        </div>

        @forelse ($roles as $role)
            @php
                $protectedRole = in_array(mb_strtolower($role->name), ['admin', 'customer', 'super admin'], true);

                $assignedPermissions = $role->permissions->pluck('name')->all();
            @endphp

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $role->name }}</h5>

                    @if ($protectedRole)
                        <span class="badge bg-secondary">System Role</span>
                    @endif
                </div>

                <div class="card-body">
                    @if ($protectedRole)
                        <p class="text-muted mb-0">
                            This system role is managed separately.
                        </p>
                    @else
                        <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="role-name-{{ $role->id }}" class="form-label">
                                    Role Name
                                </label>

                                <input id="role-name-{{ $role->id }}" type="text" name="name"
                                    value="{{ $role->name }}" class="form-control" maxlength="100" required>
                            </div>

                            <div class="row">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input id="role-{{ $role->id }}-permission-{{ $permission->id }}"
                                                type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                class="form-check-input" @checked(in_array($permission->name, $assignedPermissions, true))>

                                            <label for="role-{{ $role->id }}-permission-{{ $permission->id }}"
                                                class="form-check-label">
                                                {{ ucwords($permission->name) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="mt-3"
                            onsubmit="return confirm('Delete this role?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                Delete Role
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                No roles have been created.
            </div>
        @endforelse
    </div>
@endsection
